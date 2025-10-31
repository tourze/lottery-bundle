<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetAllLotteryChance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

/**
 * @internal
 */
#[CoversClass(GetAllLotteryChance::class)]
#[RunTestsInSeparateProcesses]
final class GetAllLotteryChanceTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testServiceExists(): void
    {
        $procedure = self::getService(GetAllLotteryChance::class);
        $this->assertInstanceOf(GetAllLotteryChance::class, $procedure);
        $this->assertInstanceOf(CacheableProcedure::class, $procedure);
    }

    public function testDefaultPropertiesHaveCorrectValues(): void
    {
        $procedure = self::getService(GetAllLotteryChance::class);

        $this->assertSame(50, $procedure->pageSize);
    }

    public function testSetPropertiesWorksCorrectly(): void
    {
        $procedure = self::getService(GetAllLotteryChance::class);

        $procedure->activityId = 'test-activity';
        $procedure->pageSize = 100;

        $this->assertSame('test-activity', $procedure->activityId);
        $this->assertSame(100, $procedure->pageSize);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(GetAllLotteryChance::class);

        $reflection = new \ReflectionMethod($procedure, 'execute');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals('array', $reflection->getReturnType()?->__toString());
    }
}
