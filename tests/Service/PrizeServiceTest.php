<?php

namespace LotteryBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\DecidePrizeProbabilityEvent;
use LotteryBundle\Service\PrizeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\EcolBundle\Service\Engine;
use Tourze\ResourceManageBundle\Service\ResourceManager;

/**
 * @group needs-refactoring
 */
class PrizeServiceTest extends TestCase
{
    private ResourceManager|MockObject $resourceManager;
    private Engine|MockObject $engine;
    private LoggerInterface|MockObject $logger;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private EntityManagerInterface|MockObject $entityManager;
    private PrizeService $prizeService;

    protected function setUp(): void
    {
        $this->resourceManager = $this->createMock(ResourceManager::class);
        $this->engine = $this->createMock(Engine::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->prizeService = new PrizeService(
            $this->resourceManager,
            $this->engine,
            $this->logger,
            $this->eventDispatcher,
            $this->entityManager
        );
    }

    /**
     * 测试奖品分发基本功能
     */
    public function test_dispatch_basicFunctionality(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $pool = $this->createMock(Pool::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置奖品属性
        $prize->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        
        $prize->expects($this->once())
            ->method('getProbability')
            ->willReturn(100);
        
        $prize->expects($this->once())
            ->method('getProbabilityExpression')
            ->willReturn(null);
        
        $prize->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        
        $prize->expects($this->once())
            ->method('getName')
            ->willReturn('测试奖品');
        
        $prize->expects($this->once())
            ->method('getQuantity')
            ->willReturn(10);
        
        // 设置奖池
        $prizes = new \Doctrine\Common\Collections\ArrayCollection([$prize]);
        $pool->expects($this->exactly(2))
            ->method('getPrizes')
            ->willReturn($prizes);
        
        // 设置 Chance 对象
        $chance->expects($this->exactly(2))
            ->method('getPool')
            ->willReturn($pool);
        
        $chance->expects($this->once())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $chance->expects($this->once())
            ->method('setProbabilityContext')
            ->with($this->isType('array'));
        
        $chance->expects($this->once())
            ->method('setPrize')
            ->with($this->identicalTo($prize));
        
        $chance->expects($this->once())
            ->method('setStatus')
            ->with(ChanceStatusEnum::WINNING);
        
        // 验证事件分发
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(DecidePrizeProbabilityEvent::class));
        
        // 执行测试
        $this->prizeService->dispatch($chance);
    }

    /**
     * 测试使用概率表达式
     */
    public function test_dispatch_withProbabilityExpression(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $pool = $this->createMock(Pool::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置奖品属性
        $prize->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        
        $prize->expects($this->once())
            ->method('getProbability')
            ->willReturn(50);
        
        $prize->expects($this->exactly(2))
            ->method('getProbabilityExpression')
            ->willReturn('rate * 2'); // 表达式将概率翻倍
        
        $prize->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        
        $prize->expects($this->once())
            ->method('getName')
            ->willReturn('测试奖品');
        
        $prize->expects($this->once())
            ->method('getQuantity')
            ->willReturn(10);
        
        // 设置奖池
        $prizes = new \Doctrine\Common\Collections\ArrayCollection([$prize]);
        $pool->expects($this->exactly(2))
            ->method('getPrizes')
            ->willReturn($prizes);
        
        // 设置 Chance 对象
        $chance->expects($this->exactly(2))
            ->method('getPool')
            ->willReturn($pool);
        
        $chance->expects($this->once())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        // 设置表达式引擎
        $this->engine->expects($this->once())
            ->method('evaluate')
            ->with('rate * 2', $this->callback(function ($context) {
                return $context['rate'] === 50;
            }))
            ->willReturn(100);
        
        // 执行测试
        $this->prizeService->dispatch($chance);
    }

    /**
     * 测试表达式计算错误的情况
     */
    public function test_dispatch_withExpressionError(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $pool = $this->createMock(Pool::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置奖品属性
        $prize->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        
        $prize->expects($this->once())
            ->method('getProbability')
            ->willReturn(50);
        
        $prize->expects($this->exactly(2))
            ->method('getProbabilityExpression')
            ->willReturn('invalid_expression');
        
        $prize->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        
        $prize->expects($this->once())
            ->method('getName')
            ->willReturn('测试奖品');
        
        $prize->expects($this->once())
            ->method('getQuantity')
            ->willReturn(10);
        
        // 设置奖池
        $prizes = new \Doctrine\Common\Collections\ArrayCollection([$prize]);
        $pool->expects($this->any())
            ->method('getPrizes')
            ->willReturn($prizes);
        
        // 设置 Chance 对象
        $chance->expects($this->any())
            ->method('getPool')
            ->willReturn($pool);
        
        $chance->expects($this->once())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        // 设置表达式引擎抛出异常
        $this->engine->expects($this->once())
            ->method('evaluate')
            ->willThrowException(new \Exception('表达式错误'));
        
        // 期望记录错误日志
        $this->logger->expects($this->once())
            ->method('error')
            ->with('奖品判断规则出错', $this->anything());
        
        // 执行测试
        $this->prizeService->dispatch($chance);
    }

    /**
     * 测试发送奖品功能
     */
    public function test_sendPrize_successfulSend(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置奖品属性
        $prize->expects($this->once())
            ->method('isNeedReview')
            ->willReturn(false);
        
        $prize->expects($this->once())
            ->method('getType')
            ->willReturn('virtual');
        
        $prize->expects($this->once())
            ->method('getTypeId')
            ->willReturn('1001');
        
        $prize->expects($this->once())
            ->method('getAmount')
            ->willReturn(1);
        
        $prize->expects($this->once())
            ->method('getExpireDay')
            ->willReturn(30.0);
        
        $prize->expects($this->once())
            ->method('getExpireTime')
            ->willReturn(null);
        
        // 设置活动结束时间
        $endTime = (new \DateTimeImmutable())->add(new \DateInterval('P60D'));
        $activity->expects($this->once())
            ->method('getEndTime')
            ->willReturn($endTime);
        
        // 设置 Chance 对象
        $chance->expects($this->once())
            ->method('getPrize')
            ->willReturn($prize);
        
        $chance->expects($this->never())
            ->method('getStatus');
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $chance->expects($this->once())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->once())
            ->method('setSendTime')
            ->with($this->isInstanceOf(\DateTimeInterface::class));
        
        $chance->expects($this->once())
            ->method('setStatus')
            ->with(ChanceStatusEnum::SENT);
        
        // 设置资源管理器
        $this->resourceManager->expects($this->once())
            ->method('send')
            ->with(
                $this->identicalTo($user),
                'virtual',
                '1001',
                1,
                30.0,
                $endTime
            );
        
        // 设置实体管理器
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($chance);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->prizeService->sendPrize($chance);
    }

    /**
     * 测试发送需要审核的奖品
     */
    public function test_sendPrize_withNeedReview_notReviewed(): void
    {
        // 创建模拟对象
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置奖品需要审核
        $prize->expects($this->once())
            ->method('isNeedReview')
            ->willReturn(true);
        
        // 设置 Chance 对象
        $chance->expects($this->once())
            ->method('getPrize')
            ->willReturn($prize);
        
        $chance->expects($this->exactly(2))
            ->method('getStatus')
            ->willReturn(ChanceStatusEnum::WINNING); // 未审核状态
        
        $chance->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        
        // 期望记录错误日志
        $this->logger->expects($this->once())
            ->method('error')
            ->with('中奖奖品需要审核但未审核，不发奖', $this->anything());
        
        // 不应该调用资源管理器
        $this->resourceManager->expects($this->never())
            ->method('send');
        
        // 不应该调用实体管理器
        $this->entityManager->expects($this->never())
            ->method('persist');
        
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试
        $this->prizeService->sendPrize($chance);
    }

    /**
     * 测试发送奖品时出现异常
     */
    public function test_sendPrize_withException(): void
    {
        // 创建模拟对象
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);
        
        // 设置奖品属性
        $prize->expects($this->once())
            ->method('isNeedReview')
            ->willReturn(false);
        
        $prize->expects($this->once())
            ->method('getType')
            ->willReturn('virtual');
        
        $prize->expects($this->once())
            ->method('getTypeId')
            ->willReturn('1001');
        
        $prize->expects($this->once())
            ->method('getAmount')
            ->willReturn(1);
        
        $prize->expects($this->once())
            ->method('getExpireDay')
            ->willReturn(30.0);
        
        $prize->expects($this->once())
            ->method('getExpireTime')
            ->willReturn(null);
        
        // 设置活动结束时间
        $endTime = (new \DateTimeImmutable())->add(new \DateInterval('P60D'));
        $activity->expects($this->once())
            ->method('getEndTime')
            ->willReturn($endTime);
        
        // 设置 Chance 对象
        $chance->expects($this->once())
            ->method('getPrize')
            ->willReturn($prize);
        
        $chance->expects($this->never())
            ->method('getStatus');
        
        $chance->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
        
        $chance->expects($this->once())
            ->method('getActivity')
            ->willReturn($activity);
        
        $chance->expects($this->never())
            ->method('setSendTime');
        
        $chance->expects($this->never())
            ->method('setStatus');
        
        $chance->expects($this->once())
            ->method('setSendResult')
            ->with($this->callback(function ($result) {
                return isset($result['exception']);
            }));
        
        // 设置资源管理器抛出异常
        $this->resourceManager->expects($this->once())
            ->method('send')
            ->willThrowException(new \Exception('发奖失败'));
        
        // 期望记录错误日志
        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('resourceManager发奖失败'));
        
        // 设置实体管理器
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($chance);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->prizeService->sendPrize($chance);
    }
}