<?php

namespace LotteryBundle\Service;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\DecidePrizeProbabilityEvent;
use LuckyBox\Card\IdCard;
use LuckyBox\LuckyBox;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tourze\BacktraceHelper\ExceptionPrinter;
use Tourze\EcolBundle\Service\Engine;
use Tourze\ResourceManageBundle\Service\ResourceManager;

class PrizeService
{
    public function __construct(
        private readonly ResourceManager $resourceManager,
        private readonly Engine $engine,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function dispatch(Chance $chance): void
    {
        // 从奖池中随机抽取（纯随机），也可以换一个lib  https://github.com/irfaardy/php-gacha
        // 如果想认为操纵概率，实现N次必中那种操作，在这里调整概率即可
        // 这种算法，会出现一种情况，就是某个奖品没库存了也能被抽中
        $luckyBox = new LuckyBox();
        $probabilityContext = [];
        $expressionValues = [
            'chance' => $chance,
            'activity' => $chance->getActivity(),
            'user' => $chance->getUser(),
        ];
        foreach ($chance->getPool()->getPrizes() as $idx => $prize) {
            if (!$prize->isValid()) {
                continue;
            }
            $card = new IdCard();
            $card->setId($idx);

            // 决定每个奖品的概率
            $decidePrizeProbabilityEvent = new DecidePrizeProbabilityEvent();
            $decidePrizeProbabilityEvent->setChance($chance);
            $decidePrizeProbabilityEvent->setPrize($prize);

            $rate = $prize->getProbability();
            $description = null;
            if (!empty($prize->getProbabilityExpression())) {
                // 如果有配置表达式，我们尝试根据表达式来再计算一次概率
                $expressionValues['rate'] = $rate;
                try {
                    $rate = $this->engine->evaluate($prize->getProbabilityExpression(), $expressionValues);
                    if (is_bool($rate) && !$rate) {
                        $this->logger->info('根据表达式返回值得出这个奖项不能抽中', [
                            'prize' => $prize,
                        ]);
                        $rate = 0;
                    } else {
                        $rate = intval($rate);
                    }
                } catch (\Throwable $exception) {
                    $this->logger->error('奖品判断规则出错', [
                        'exception' => $exception,
                        'values' => $expressionValues,
                    ]);
                    $rate = 0; // 如果一个奖品配置了规则，同时规则出错的话，我们要将规格改成0，防止出现问题
                    $description = '表达式判断出错';
                }
            }

            if ($rate < 0) {
                $this->logger->error('出现未知异常，概率不应该小于0', [
                    'rate' => $rate,
                    'chance' => $chance,
                    'prize' => $prize,
                ]);
                $description = "异常概率数:{$rate}";
                $rate = 0;
            }

            $decidePrizeProbabilityEvent->setRate($rate);
            $this->eventDispatcher->dispatch($decidePrizeProbabilityEvent);

            $card->setRate($decidePrizeProbabilityEvent->getRate());
            $probabilityContext[] = [
                'id' => $prize->getId(),
                'name' => $prize->getName(),
                'rate' => $decidePrizeProbabilityEvent->getRate(),
                'quantity' => $prize->getQuantity(),
                'description' => $description,
            ];
            $luckyBox->add($card);
        }

        // 记录下抽奖时的概率情况
        $chance->setProbabilityContext($probabilityContext);

        $card = $luckyBox->draw();
        if ($card) {
            $prize = $chance->getPool()->getPrizes()->get($card->getId());
            $chance->setPrize($prize);
            $chance->setStatus(ChanceStatusEnum::WINNING);
        }
    }

    /**
     * 发奖品的详细逻辑
     */
    public function sendPrize(Chance $chance): void
    {
        $now = Carbon::now();
        $prize = $chance->getPrize();
        // 需审核的奖品不自动发
        // var_dump($prize->isNeedReview(),$chance->getStatus());
        if ($prize->isNeedReview() && ChanceStatusEnum::REVIEWED !== $chance->getStatus()) {
            $this->logger->error('中奖奖品需要审核但未审核，不发奖', [
                'id' => $chance->getId(),
                'status' => $chance->getStatus()->value,
            ]);

            return;
        }

        try {
            $this->resourceManager->send(
                $chance->getUser(),
                $prize->getType(),
                $prize->getTypeId(),
                $prize->getAmount(),
                $prize->getExpireDay(),
                $prize->getExpireTime() ?? $chance->getActivity()->getEndTime(),
            );

            $chance->setSendTime($now);
            $chance->setStatus(ChanceStatusEnum::SENT);
        } catch (\Throwable $exception) {
            $exception->getMessage();
            $this->logger->error('resourceManager发奖失败：' . $exception->getMessage());
            $chance->setSendResult([
                'exception' => ExceptionPrinter::exception($exception),
            ]);
        }
        $this->entityManager->persist($chance);
        $this->entityManager->flush();
    }
}
