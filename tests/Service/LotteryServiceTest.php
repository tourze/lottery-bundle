<?php

namespace LotteryBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Event\ChanceEvent;
use LotteryBundle\Exception\LotteryException;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Repository\PrizeRepository;
use LotteryBundle\Service\LotteryService;
use LotteryBundle\Service\PoolService;
use LotteryBundle\Service\PrizeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @group needs-refactoring
 */
class LotteryServiceTest extends TestCase
{
    private ChanceRepository|MockObject $chanceRepository;
    private PrizeRepository|MockObject $prizeRepository;
    private LoggerInterface|MockObject $logger;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private PoolService|MockObject $poolService;
    private PrizeService|MockObject $prizeService;
    private EntityManagerInterface|MockObject $entityManager;
    private LotteryService $lotteryService;

    protected function setUp(): void
    {
        $this->chanceRepository = $this->createMock(ChanceRepository::class);
        $this->prizeRepository = $this->createMock(PrizeRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->poolService = $this->createMock(PoolService::class);
        $this->prizeService = $this->createMock(PrizeService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->lotteryService = new LotteryService(
            $this->chanceRepository,
            $this->prizeRepository,
            $this->logger,
            $this->eventDispatcher,
            $this->poolService,
            $this->prizeService,
            $this->entityManager
        );
    }

    /**
     * 测试抽奖基本流程
     */
    public function test_doLottery_basicFlow(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $pool = $this->createMock(Pool::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置 Chance 对象
        $chance->expects($this->any())
            ->method('getPrize')
            ->willReturnCallback(function() use ($prize) {
                static $callCount = 0;
                $callCount++;
                if ($callCount <= 2) {
                    return null;
                }
                return $prize;
            });
        
        $chance->expects($this->once())
            ->method('getPool')
            ->willReturn($pool);
        
        $chance->expects($this->any())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $chance->expects($this->once())
            ->method('setValid')
            ->with(false);
        
        $chance->expects($this->once())
            ->method('setUseTime')
            ->with($this->isInstanceOf(\DateTimeInterface::class));
        
        $user->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_user');
        
        // 设置奖品
        $prize->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        
        $prize->expects($this->any())
            ->method('getDayLimit')
            ->willReturn(0);
        
        $prize->expects($this->any())
            ->method('getQuantity')
            ->willReturn(10);
        
        // 设置奖池服务
        $this->poolService->expects($this->once())
            ->method('dispatch')
            ->with($chance);
        
        // 设置奖品服务
        $this->prizeService->expects($this->once())
            ->method('dispatch')
            ->with($chance);
        
        $this->prizeService->expects($this->once())
            ->method('sendPrize')
            ->with($chance);
        
        // 设置奖品仓库
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->prizeRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder->expects($this->once())
            ->method('update')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('set')
            ->with('a.quantity', 'a.quantity - 1')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('a.id = :id AND a.quantity > 0')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('id', 1)
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $query->expects($this->once())
            ->method('execute')
            ->willReturn(1);
        
        // 设置实体管理器
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($chance);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->lotteryService->doLottery($chance);
    }

    /**
     * 测试奖品库存不足的情况
     */
    public function test_doLottery_withInsufficientStock_throwsException(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $pool = $this->createMock(Pool::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置 Chance 对象
        $chance->expects($this->any())
            ->method('getPrize')
            ->willReturnCallback(function() use ($prize) {
                static $callCount = 0;
                $callCount++;
                if ($callCount <= 2) {
                    return null;
                }
                return $prize;
            });
        
        $chance->expects($this->once())
            ->method('getPool')
            ->willReturn($pool);
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $user->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_user');
        
        // 设置奖品
        $prize->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        
        $prize->expects($this->any())
            ->method('getDayLimit')
            ->willReturn(0);
        
        $prize->expects($this->any())
            ->method('getQuantity')
            ->willReturn(10);
        
        $prize->expects($this->once())
            ->method('getName')
            ->willReturn('测试奖品');
        
        // 设置奖池服务
        $this->poolService->expects($this->once())
            ->method('dispatch')
            ->with($chance);
        
        // 设置奖品服务
        $this->prizeService->expects($this->once())
            ->method('dispatch')
            ->with($chance);
        
        // 设置奖品仓库 - 返回0表示更新失败，库存不足
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->prizeRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder->expects($this->once())
            ->method('update')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('set')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $query->expects($this->once())
            ->method('execute')
            ->willReturn(0);
        
        // 期望记录日志
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('抽到奖品'));
        
        // 期望设置 prize 为 null
        $chance->expects($this->once())
            ->method('setPrize')
            ->with(null);
        
        // 期望抛出异常
        $this->expectException(LotteryException::class);
        $this->expectExceptionMessage('[测试奖品]库存不足');
        
        // 执行测试
        $this->lotteryService->doLottery($chance);
    }

    /**
     * 测试奖品已达每日限制的情况
     */
    public function test_doLottery_withDailyLimitReached(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $pool = $this->createMock(Pool::class);
        $prize = $this->createMock(Prize::class);
        $defaultPrize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置 Chance 对象
        $chance->expects($this->any())
            ->method('getPrize')
            ->willReturnOnConsecutiveCalls(
                null, // 第1次调用
                null, // 第2次调用  
                $prize, // 第3次调用 - dispatch 后有奖品
                $prize, // 第4次调用 - 检查 day limit
                $prize, // 第5次调用 - 再次检查
                $prize, // 第6次调用 - 检查库存
                $defaultPrize, // 第7次调用 - 设置默认奖品后
                $defaultPrize, // 第8次调用
                $defaultPrize, // 第9次调用
                $defaultPrize  // 第10次调用
            );
        
        $chance->expects($this->any())
            ->method('getPool')
            ->willReturn($pool);
        
        $chance->expects($this->any())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $chance->expects($this->once())
            ->method('setValid')
            ->with(false);
        
        $chance->expects($this->once())
            ->method('setUseTime')
            ->with($this->isInstanceOf(\DateTimeInterface::class));
        
        $chance->expects($this->once())
            ->method('setPrize')
            ->with($defaultPrize);
        
        $chance->expects($this->once())
            ->method('setStatus')
            ->with(\LotteryBundle\Enum\ChanceStatusEnum::WINNING);
        
        $user->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('test_user');
        
        // 设置奖品
        $prize->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        
        $prize->expects($this->any())
            ->method('getDayLimit')
            ->willReturn(5);
        
        // 设置奖池
        $prizes = new \Doctrine\Common\Collections\ArrayCollection([$defaultPrize]);
        $pool->expects($this->once())
            ->method('getPrizes')
            ->willReturn($prizes);
        
        // 设置默认奖品
        $defaultPrize->expects($this->once())
            ->method('getIsDefault')
            ->willReturn(true);
        
        $defaultPrize->expects($this->any())
            ->method('getId')
            ->willReturn(2);
        
        // 设置奖池服务
        $this->poolService->expects($this->once())
            ->method('dispatch')
            ->with($chance);
        
        // 设置奖品服务
        $this->prizeService->expects($this->once())
            ->method('dispatch')
            ->with($chance);
        
        // 设置机会仓库 - 模拟每日已达到限制
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->chanceRepository->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->any())
            ->method('where')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->any())
            ->method('setParameter')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        
        $query->expects($this->any())
            ->method('getSingleScalarResult')
            ->willReturn(5); // 已达到每日限制
        
        // 期望记录日志
        $this->logger->expects($this->any())
            ->method('info');
        
        // 设置奖品仓库 - 更新默认奖品库存
        $prizeQueryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $prizeQuery = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->prizeRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($prizeQueryBuilder);
        
        $prizeQueryBuilder->expects($this->once())
            ->method('update')
            ->willReturnSelf();
        
        $prizeQueryBuilder->expects($this->once())
            ->method('set')
            ->willReturnSelf();
        
        $prizeQueryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        
        $prizeQueryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('id', 2)
            ->willReturnSelf();
        
        $prizeQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($prizeQuery);
        
        $prizeQuery->expects($this->once())
            ->method('execute')
            ->willReturn(1);
        
        // 执行测试
        $this->lotteryService->doLottery($chance);
    }

    /**
     * 测试发放抽奖机会
     */
    public function test_giveChance_success(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置 Chance 对象
        $chance->expects($this->once())
            ->method('setUser')
            ->with($user);
        
        $chance->expects($this->once())
            ->method('setStartTime')
            ->with($this->isInstanceOf(\DateTimeInterface::class));
        
        // getActivity() 在 giveChance 方法中不会被调用
        
        // 验证事件分发
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ChanceEvent::class));
        
        // 设置实体管理器
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($chance);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->lotteryService->giveChance($user, $chance);
    }

    /**
     * 测试计算有效抽奖次数
     */
    public function test_countValidChance_returnsCorrectCount(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        
        // 设置机会仓库
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $this->chanceRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder->expects($this->once())
            ->method('select')
            ->with('COUNT(a.id)')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('a.user = :user AND a.activity = :activity AND a.valid = true')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $query->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn(5);
        
        // 执行测试
        $result = $this->lotteryService->countValidChance($user, $activity);
        
        // 验证结果
        $this->assertEquals(5, $result);
    }
}