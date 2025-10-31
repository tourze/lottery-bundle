<?php

declare(strict_types=1);

namespace LotteryBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\DecidePrizeProbabilityEvent;
use LotteryBundle\Exception\PrizeServiceException;
use LuckyBox\Card\IdCard;
use LuckyBox\LuckyBox;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tourze\BacktraceHelper\ExceptionPrinter;
use Tourze\EcolBundle\Service\Engine;
use Tourze\ResourceManageBundle\Service\ResourceManager;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'lottery')]
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

    /**
     * 不考虑并发 - 抽奖算法为随机操作，不影响数据一致性
     */
    public function dispatch(Chance $chance): void
    {
        $luckyBox = new LuckyBox();
        $probabilityContext = [];
        $expressionValues = $this->buildExpressionValues($chance);

        $pool = $chance->getPool();
        if (null === $pool) {
            throw PrizeServiceException::chancePoolCannotBeNull();
        }

        foreach ($pool->getPrizes() as $idx => $prize) {
            if (true !== $prize->isValid()) {
                continue;
            }

            $card = $this->createPrizeCard($idx);
            $rate = $this->calculatePrizeRate($prize, $chance, $expressionValues);
            $probabilityContext[] = $this->buildPrizeContext($prize, $rate);

            $card->setRate($rate);
            $luckyBox->add($card);
        }

        $this->recordProbabilityContext($chance, $probabilityContext);
        $this->drawPrize($chance, $luckyBox);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildExpressionValues(Chance $chance): array
    {
        return [
            'chance' => $chance,
            'activity' => $chance->getActivity(),
            'user' => $chance->getUser(),
        ];
    }

    /**
     * 不考虑并发 - 创建内存对象
     */
    private function createPrizeCard(int $idx): IdCard
    {
        $card = new IdCard();
        $card->setId($idx);

        return $card;
    }

    /**
     * 不考虑并发 - 概率计算为纯数学操作
     */
    /**
     * @param array<string, mixed> $expressionValues
     */
    private function calculatePrizeRate(
        Prize $prize,
        Chance $chance,
        array $expressionValues,
    ): int {
        // 决定每个奖品的概率
        $decidePrizeProbabilityEvent = new DecidePrizeProbabilityEvent();
        $decidePrizeProbabilityEvent->setChance($chance);
        $decidePrizeProbabilityEvent->setPrize($prize);

        $rate = $prize->getProbability();

        if (null !== $prize->getProbabilityExpression() && '' !== $prize->getProbabilityExpression()) {
            $rate = $this->evaluateExpression($prize, $expressionValues, $rate ?? 0, $chance);
        }

        $rate = $this->validateRate($rate ?? 0, $chance, $prize);

        $decidePrizeProbabilityEvent->setRate($rate);
        $this->eventDispatcher->dispatch($decidePrizeProbabilityEvent);

        return (int) $decidePrizeProbabilityEvent->getRate();
    }

    /**
     * @param array<string, mixed> $expressionValues
     */
    private function evaluateExpression(
        Prize $prize,
        array $expressionValues,
        int $originalRate,
        Chance $chance,
    ): int {
        $expressionValues['rate'] = $originalRate;

        $expression = $prize->getProbabilityExpression();
        if (null === $expression) {
            return $originalRate;
        }

        try {
            $rate = $this->engine->evaluate($expression, $expressionValues);

            if (is_bool($rate) && !$rate) {
                $this->logger->info('根据表达式返回值得出这个奖项不能抽中', ['prize' => $prize]);

                return 0;
            }

            assert(is_int($rate) || is_string($rate) || is_float($rate) || is_bool($rate) || null === $rate);

            return $this->validateRate(intval($rate), $chance, $prize);
        } catch (\Throwable $exception) {
            $this->logger->error('奖品判断规则出错', [
                'exception' => $exception,
                'values' => $expressionValues,
            ]);

            return 0; // 如果一个奖品配置了规则，同时规则出错的话，我们要将规格改成0，防止出现问题
        }
    }

    private function validateRate(int $rate, Chance $chance, Prize $prize): int
    {
        if ($rate < 0) {
            $this->logger->error('出现未知异常，概率不应该小于0', [
                'rate' => $rate,
                'chance' => $chance,
                'prize' => $prize,
            ]);

            return 0;
        }

        return $rate;
    }

    /**
     * 不考虑并发 - 构建上下文数据为纯内存操作
     */
    /**
     * @return array<string, mixed>
     */
    private function buildPrizeContext(Prize $prize, int $rate): array
    {
        return [
            'id' => $prize->getId(),
            'name' => $prize->getName(),
            'rate' => $rate,
            'quantity' => $prize->getQuantity(),
            'description' => null,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $probabilityContext
     */
    private function recordProbabilityContext(Chance $chance, array $probabilityContext): void
    {
        $chance->setProbabilityContext($probabilityContext);
    }

    /**
     * 不考虑并发 - 抽奖操作为随机算法，结果存储在对象中
     */
    private function drawPrize(Chance $chance, LuckyBox $luckyBox): void
    {
        $card = $luckyBox->draw();
        if ($card instanceof IdCard) {
            $pool = $chance->getPool();
            if (null !== $pool) {
                $cardId = $card->getId();
                assert(is_int($cardId));

                $prize = $pool->getPrizes()->get($cardId);
                if ($prize instanceof Prize) {
                    $chance->setPrize($prize);
                    $chance->setStatus(ChanceStatusEnum::WINNING);
                }
            }
        }
    }

    /**
     * 发奖品的详细逻辑
     *
     * 注意：此方法涉及并发敏感操作（资源发放和状态更新）
     * 通过外部资源管理器确保发奖操作的原子性
     * 如果发奖失败，会记录错误信息但不回滚抽奖结果
     *
     * 不考虑并发 - 发奖由 ResourceManager 统一管理并发安全
     */
    public function sendPrize(Chance $chance): void
    {
        $now = CarbonImmutable::now();
        $prize = $chance->getPrize();

        if (null === $prize) {
            $this->logger->info('奖品为空，跳过发奖', ['chance_id' => $chance->getId()]);

            return;
        }

        if ($this->shouldSkipSending($prize, $chance)) {
            return;
        }

        $this->executePrizeSending($chance, $prize, $now);
        $this->saveSendingResult($chance);
    }

    private function shouldSkipSending(Prize $prize, Chance $chance): bool
    {
        // 需审核的奖品不自动发
        if (true === $prize->isNeedReview() && ChanceStatusEnum::REVIEWED !== $chance->getStatus()) {
            $this->logger->error('中奖奖品需要审核但未审核，不发奖', [
                'id' => $chance->getId(),
                'status' => $chance->getStatus()?->value,
            ]);

            return true;
        }

        return false;
    }

    /**
     * 不考虑并发 - 发奖由 ResourceManager 统一管理并发安全
     */
    private function executePrizeSending(Chance $chance, Prize $prize, CarbonImmutable $now): void
    {
        try {
            $user = $chance->getUser();
            $activity = $chance->getActivity();
            $typeId = $prize->getTypeId();

            if (null === $user) {
                throw PrizeServiceException::chanceUserCannotBeNull();
            }

            if (null === $typeId) {
                throw PrizeServiceException::prizeTypeIdCannotBeNull();
            }

            if (null === $activity) {
                throw PrizeServiceException::chanceActivityCannotBeNull();
            }

            $this->resourceManager->send(
                $user,
                $prize->getType(),
                $typeId,
                (string) $prize->getAmount(),
                $prize->getExpireDay(),
                $prize->getExpireTime() ?? $activity->getEndTime(),
            );

            $chance->setSendTime($now);
            $chance->setStatus(ChanceStatusEnum::SENT);
        } catch (\Throwable $exception) {
            $this->logger->error('resourceManager发奖失败：' . $exception->getMessage());
            $chance->setSendResult([
                'exception' => ExceptionPrinter::exception($exception),
            ]);
        }
    }

    private function saveSendingResult(Chance $chance): void
    {
        $this->entityManager->persist($chance);
        $this->entityManager->flush();
    }
}
