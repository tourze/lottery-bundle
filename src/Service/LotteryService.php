<?php

namespace LotteryBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\ChanceEvent;
use LotteryBundle\Exception\LotteryException;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Repository\PrizeRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Autoconfigure(lazy: true, public: true)]
#[WithMonologChannel(channel: 'lottery')]
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
     *
     * 注意：此方法涉及并发敏感操作（库存扣减），已通过数据库乐观锁控制
     * 使用了 UPDATE ... WHERE a.quantity > 0 语句确保库存扣减的原子性
     * 在高并发场景下可能出现库存不足异常，需要外层处理重试逻辑
     *
     * 不考虑并发 - 关键库存扣减已通过数据库乐观锁处理
     */
    public function doLottery(Chance $chance): void
    {
        $now = CarbonImmutable::now();

        $this->ensurePrizeAndPool($chance);
        $prizeReachDayLimit = $this->checkDayLimit($chance, $now);
        $this->handlePrizeUnavailable($chance, $prizeReachDayLimit);
        $this->validatePrizeExists($chance);
        $this->logPrizeWon($chance);
        $this->decreaseStock($chance);
        $this->saveChanceResult($chance, $now);
        $this->sendPrize($chance);
    }

    /**
     * 不考虑并发 - 奖品和奖池分配为内存操作
     */
    private function ensurePrizeAndPool(Chance $chance): void
    {
        // 有可能提前定好了奖品
        if (null === $chance->getPrize()) {
            // 分配奖池
            $this->poolService->dispatch($chance);
            // 抽取奖品
            $this->prizeService->dispatch($chance);
        }

        // 有一种可能就是定了奖品，但是没定奖池，我们这里要补充下
        if (null === $chance->getPool() && null !== $chance->getPrize()) {
            $chance->setPool($chance->getPrize()->getPool());
        }
    }

    private function checkDayLimit(Chance $chance, CarbonImmutable $now): bool
    {
        if (null === $chance->getPrize() || $chance->getPrize()->getDayLimit() <= 0) {
            return false;
        }

        $prize = $chance->getPrize();
        $count = $this->chanceRepository->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.activity = :activity and c.prize = :prize and c.useTime between :start and :end')
            ->setParameter('activity', $chance->getActivity())
            ->setParameter('prize', $prize)
            ->setParameter('start', $now->clone()->startOfDay())
            ->setParameter('end', $now->clone()->endOfDay())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if ($count >= $prize->getDayLimit()) {
            $this->logger->info("{$prize->getId()} 奖品已达到每日限制数量", [
                'dayLimit' => $prize->getDayLimit(),
                'hadCount' => $count,
            ]);

            return true;
        }

        return false;
    }

    /**
     * 不考虑并发 - 奖品不可用处理为业务逻辑判断
     */
    private function handlePrizeUnavailable(Chance $chance, bool $prizeReachDayLimit): void
    {
        // 如果到这里还没抽中，或抽到的奖品没有库存，或已达到每日限制数量，同时有设置了兜底奖品那么我们就当做做了兜底奖品
        if (null === $chance->getPrize() || $chance->getPrize()->getQuantity() <= 0 || $prizeReachDayLimit) {
            $this->setDefaultPrize($chance, $prizeReachDayLimit);
        }
    }

    /**
     * 不考虑并发 - 设置兜底奖品为内存操作
     */
    private function setDefaultPrize(Chance $chance, bool $prizeReachDayLimit): void
    {
        $this->logger->info('符合以下条件，中兜底奖品', [
            'prize' => $chance->getPrize(),
            'quantity' => $chance->getPrize()?->getQuantity(),
            'prizeReachDayLimit' => $prizeReachDayLimit,
        ]);

        $defaultPrizes = $this->findDefaultPrizes($chance);

        if ([] !== $defaultPrizes) {
            $this->selectRandomDefaultPrize($chance, $defaultPrizes);
        } else {
            $this->logger->warning('兜底奖品都无法查找得到', ['chance' => $chance]);
            $message = $_ENV['LOTTERY_JOIN_EMPTY_MESSAGE'] ?? '抽奖失败，找不到任何奖励';
            assert(is_string($message));
            throw new LotteryException($message);
        }
    }

    /**
     * 不考虑并发 - 查找兜底奖品为只读操作
     *
     * @return array<int, Prize>
     */
    private function findDefaultPrizes(Chance $chance): array
    {
        $pool = $chance->getPool();
        if (null === $pool) {
            return [];
        }

        $defaultPrizes = [];
        foreach ($pool->getPrizes() as $prize) {
            // 兜底奖品就不看库存
            if (true === $prize->getIsDefault()) {
                $defaultPrizes[] = $prize;
            }
        }

        return $defaultPrizes;
    }

    /**
     * 不考虑并发 - 随机选择兜底奖品为内存操作
     *
     * @param array<int, Prize> $defaultPrizes
     */
    private function selectRandomDefaultPrize(Chance $chance, array $defaultPrizes): void
    {
        $rIndex = array_rand($defaultPrizes);
        $prize = $defaultPrizes[$rIndex];

        $this->logger->info('选择一个兜底奖品', [
            'chance' => $chance,
            'prize' => $prize,
            'defaultPrizes' => $defaultPrizes,
        ]);

        $chance->setPrize($prize);
        $chance->setStatus(ChanceStatusEnum::WINNING);
    }

    /**
     * 不考虑并发 - 验证奖品存在为只读操作
     */
    private function validatePrizeExists(Chance $chance): void
    {
        if (null === $chance->getPrize()) {
            $message = $_ENV['LOTTERY_JOIN_EMPTY_MESSAGE'] ?? '抽奖失败，找不到任何奖励';
            assert(is_string($message));
            throw new LotteryException($message);
        }
    }

    /**
     * 不考虑并发 - 日志记录操作
     */
    private function logPrizeWon(Chance $chance): void
    {
        $user = $chance->getUser();
        $prize = $chance->getPrize();

        if (null === $user || null === $prize) {
            $this->logger->error('用户或奖品为空，无法记录中奖日志', [
                'user' => $user,
                'prize' => $prize,
            ]);

            return;
        }

        $this->logger->info("用户{$user->getUserIdentifier()} 抽到奖品：{$prize->getId()}", [
            'prize' => $prize,
        ]);
    }

    /**
     * 不考虑并发 - 库存扣减已通过数据库乐观锁处理并发安全
     */
    private function decreaseStock(Chance $chance): void
    {
        // 使用乐观锁扣减库存，确保并发安全
        $rs = $this->prizeRepository->createQueryBuilder('a')->update()
            ->set('a.quantity', 'a.quantity - 1')
            ->where('a.id = :id AND a.quantity > 0')
            ->setParameter('id', $chance->getPrize()?->getId())
            ->getQuery()
            ->execute()
        ;

        assert(is_int($rs) || is_string($rs) || is_float($rs));
        if (1 !== intval($rs)) {
            $prize = $chance->getPrize();
            $chance->setPrize(null);
            $prizeName = $prize?->getName() ?? 'Unknown';
            throw new LotteryException("[{$prizeName}]库存不足");
        }
    }

    private function saveChanceResult(Chance $chance, CarbonImmutable $now): void
    {
        try {
            // 记录落库
            $chance->setValid(false);
            $chance->setUseTime($now);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();

            $this->logger->info('扣减库存成功', ['change' => $chance]);
        } catch (\Throwable $exception) {
            $this->logger->error('抽奖失败', [
                'exception' => $exception,
                'change' => $chance,
            ]);
            throw new LotteryException('请重新参与抽奖！', previous: $exception);
        }
    }

    /**
     * 不考虑并发 - 发奖委托给 PrizeService 处理
     */
    private function sendPrize(Chance $chance): void
    {
        // 库存扣了之后，我们再去真实执行发奖品逻辑
        $this->prizeService->sendPrize($chance);
    }

    /**
     * 为指定用户发机会
     */
    public function giveChance(UserInterface $user, Chance $chance): void
    {
        $chance->setUser($user);
        $chance->setStartTime(CarbonImmutable::now());

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
    public function countValidChance(UserInterface $user, Activity $activity): int
    {
        $c = $this->chanceRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.user = :user AND a.activity = :activity AND a.valid = true')
            ->setParameter('user', $user)
            ->setParameter('activity', $activity)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return intval($c);
    }
}
