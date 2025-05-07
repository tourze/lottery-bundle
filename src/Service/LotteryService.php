<?php

namespace LotteryBundle\Service;

use AppBundle\Entity\BizUser;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\ChanceEvent;
use LotteryBundle\Exception\LotteryException;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Repository\PrizeRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Autoconfigure(lazy: true, public: true)]
class LotteryService
{
    public const LOTTERY_PRIZE_REDIS_COUNT = 'lottery_prize_redis_count_';

    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly PrizeRepository $prizeRepository,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PoolService $poolService,
        private readonly PrizeService $prizeService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 执行抽奖和发奖
     */
    public function doLottery(Chance $chance): void
    {
        $now = Carbon::now();

        // 有可能提前定好了奖品
        if (!$chance->getPrize()) {
            // 分配奖池
            $this->poolService->dispatch($chance);

            // 抽取奖品
            $this->prizeService->dispatch($chance);
        }
        // 有一种可能就是定了奖品，但是没定奖池，我们这里要补充下
        if (!$chance->getPool() && $chance->getPrize()) {
            $chance->setPool($chance->getPrize()->getPool());
        }

        // 如果有配置每日发放数量，这里也要判断一下
        $prizeReachDayLimit = false;
        if ($chance->getPrize() && $chance->getPrize()->getDayLimit() > 0) {
            $prize = $chance->getPrize();
            $count = $this->chanceRepository->createQueryBuilder('c')
                ->select('count(c.id)')
                ->where('c.activity = :activity and c.prize = :prize and c.useTime between :start and :end')
                ->setParameter('activity', $chance->getActivity())
                ->setParameter('prize', $prize)
                ->setParameter('start', $now->clone()->startOfDay())
                ->setParameter('end', $now->clone()->endOfDay())
                ->getQuery()
                ->getSingleScalarResult();
            if ($count >= $prize->getDayLimit()) {
                $prizeReachDayLimit = true;
                $this->logger->info("{$prize->getId()} 奖品已达到每日限制数量", [
                    'dayLimit' => $prize->getDayLimit(),
                    'hadCount' => $count,
                ]);
            }
        }

        // 如果到这里还没抽中，或抽到的奖品没有库存，或已达到每日限制数量，，同时有设置了兜底奖品那么我们就当做做了兜底奖品，减少下面的查询压力啦
        if (!$chance->getPrize() || $chance->getPrize()->getQuantity() <= 0 || $prizeReachDayLimit) {
            $this->logger->info('符合以下条件，中兜底奖品', [
                'prize' => $chance->getPrize(),
                'quantity' => $chance->getPrize()?->getQuantity(),
                'prizeReachDayLimit' => $prizeReachDayLimit,
            ]);

            // 兜底奖品也可能有多个的，所以这里要随机一下
            $defaultPrizes = [];
            foreach ($chance->getPool()->getPrizes() as $prize) {
                // 兜底奖品就不看库存
                if ($prize->getIsDefault()) {
                    $defaultPrizes[] = $prize;
                }
            }

            if (!empty($defaultPrizes)) {
                $rIndex = array_rand($defaultPrizes);
                $prize = $defaultPrizes[$rIndex];
                $this->logger->info('选择一个兜底奖品', [
                    'chance' => $chance,
                    'prize' => $prize,
                    'defaultPrizes' => $defaultPrizes,
                ]);
                $chance->setPrize($prize);
                $chance->setStatus(ChanceStatusEnum::WINNING);
            } else {
                $this->logger->warning('兜底奖品都无法查找得到', [
                    'chance' => $chance,
                ]);
                // 必须有兜底奖品
                throw new LotteryException($_ENV['LOTTERY_JOIN_EMPTY_MESSAGE'] ?? '抽奖失败，找不到任何奖励');
            }
        }

        if (!$chance->getPrize()) {
            throw new LotteryException($_ENV['LOTTERY_JOIN_EMPTY_MESSAGE'] ?? '抽奖失败，找不到任何奖励');
        }

        $this->logger->info("用户{$chance->getUser()->getUserIdentifier()} 抽到奖品：{$chance->getPrize()->getId()}", [
            'prize' => $chance->getPrize(),
        ]);

        // 改成用redis的decr解决库存问题
        // 不适用于中途更改库存的情况
        //        $redisKey = self::LOTTERY_PRIZE_REDIS_COUNT . $chance->getPrize()->getId();
        //        if (!$this->redis->get($redisKey)) {
        //            $this->redis->set($redisKey, $chance->getPrize()->getQuantity(), MONTH_IN_MINUTES * 3);
        //        }
        //        $prizeCount = $this->redis->decr($redisKey);
        //
        //        if (false != $prizeCount) {
        //            if ($prizeCount < 0 && !$chance->getPrize()->getIsDefault()) {
        //                $this->redis->incr($redisKey);
        //                $this->logger->error('抽奖失败，数量不足', [
        //                    'chance' => $chance,
        //                    'prize' => $chance->getPrize(),
        //                    'prizeCount' => $prizeCount,
        //                ]);
        //                throw new LotteryException('抽奖失败，数量不足');
        //            }

        // 减库存
        $rs = $this->prizeRepository->createQueryBuilder('a')->update()
            ->set('a.quantity', 'a.quantity - 1')
            ->where('a.id = :id AND a.quantity > 0')
            ->setParameter('id', $chance->getPrize()->getId())
            ->getQuery()
            ->execute();
        $rs = intval($rs);
        if (1 !== $rs) {
            $prize = $chance->getPrize();
            $chance->setPrize(null);
            throw new LotteryException("[{$prize->getName()}]库存不足");
        }

        try {
            // 记录落库啦
            $chance->setValid(false);
            $chance->setUseTime($now);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();
            $this->logger->info('扣减库存成功', [
                'change' => $chance,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('抽奖失败', [
                'exception' => $exception,
                'change' => $chance,
            ]);
            throw new LotteryException('请重新参与抽奖！', previous: $exception);
        }
        //        } else {
        //            throw new LotteryException('抽奖失败，请重试');
        //        }

        // 库存扣了之后，我们再去真实执行发奖品逻辑
        $this->prizeService->sendPrize($chance);
    }

    /**
     * 为指定用户发机会
     */
    public function giveChance(BizUser $user, Chance $chance): void
    {
        $chance->setUser($user);
        $chance->setStartTime(Carbon::now());

        //        这里处理奖池吧
        //        $decidePoolEvent = new DecidePoolEvent();
        //        $decidePoolEvent->setChance($chance);
        //        $decidePoolEvent->setActivity($chance->getActivity());
        //        $decidePoolEvent->setUser($user);
        //        $this->eventDispatcher->dispatch($decidePoolEvent);

        // 抽奖的额外处理
        $chanceEvent = new ChanceEvent();
        $chanceEvent->setChance($chance);
        $this->eventDispatcher->dispatch($chanceEvent);

        $this->entityManager->persist($chance);
        $this->entityManager->flush();
    }

    /**
     * 获取有效次数
     */
    public function countValidChance(BizUser $user, Activity $activity): int
    {
        $c = $this->chanceRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.user = :user AND a.activity = :activity AND a.valid = true')
            ->setParameter('user', $user)
            ->setParameter('activity', $activity)
            ->getQuery()
            ->getScalarResult();

        return intval($c);
    }
}
