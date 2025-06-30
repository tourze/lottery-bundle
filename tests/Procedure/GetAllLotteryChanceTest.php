<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetAllLotteryChance;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

class GetAllLotteryChanceTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $activityRepository = $this->createMock(ActivityRepository::class);
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $security = $this->createMock(Security::class);

        $procedure = new GetAllLotteryChance(
            $activityRepository,
            $chanceRepository,
            $eventDispatcher,
            $security
        );

        $this->assertInstanceOf(GetAllLotteryChance::class, $procedure);
        $this->assertInstanceOf(CacheableProcedure::class, $procedure);
    }

    public function test_defaultProperties_haveCorrectValues(): void
    {
        $activityRepository = $this->createMock(ActivityRepository::class);
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $security = $this->createMock(Security::class);

        $procedure = new GetAllLotteryChance(
            $activityRepository,
            $chanceRepository,
            $eventDispatcher,
            $security
        );

        $this->assertSame(50, $procedure->pageSize);
    }

    public function test_setProperties_worksCorrectly(): void
    {
        $activityRepository = $this->createMock(ActivityRepository::class);
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $security = $this->createMock(Security::class);

        $procedure = new GetAllLotteryChance(
            $activityRepository,
            $chanceRepository,
            $eventDispatcher,
            $security
        );

        $procedure->activityId = 'test-activity';
        $procedure->pageSize = 100;

        $this->assertSame('test-activity', $procedure->activityId);
        $this->assertSame(100, $procedure->pageSize);
    }
} 