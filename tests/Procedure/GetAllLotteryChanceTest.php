<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Param\GetAllLotteryChanceParam;
use LotteryBundle\Procedure\GetAllLotteryChance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

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
    }

    public function testExtendsCacheableProcedure(): void
    {
        $reflectionClass = new \ReflectionClass(GetAllLotteryChance::class);
        $this->assertTrue($reflectionClass->isSubclassOf(CacheableProcedure::class));
    }

    public function testParamHasCorrectDefaults(): void
    {
        $param = new GetAllLotteryChanceParam('test-activity');

        $this->assertSame('test-activity', $param->activityId);
        $this->assertSame(50, $param->pageSize);
    }

    public function testParamWithCustomPageSize(): void
    {
        $param = new GetAllLotteryChanceParam('test-activity', 100);

        $this->assertSame('test-activity', $param->activityId);
        $this->assertSame(100, $param->pageSize);
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(GetAllLotteryChance::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheKey'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheDuration'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheTags'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetAllLotteryChance::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testGetCacheKeyMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetAllLotteryChance::class, 'getCacheKey');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    public function testGetCacheDurationMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetAllLotteryChance::class, 'getCacheDuration');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('int', $returnType->getName());
    }

    public function testGetCacheTagsMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetAllLotteryChance::class, 'getCacheTags');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('iterable', $returnType->getName());
    }

    public function testConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(GetAllLotteryChance::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(4, $params);

        $this->assertEquals('activityRepository', $params[0]->getName());
        $this->assertEquals('chanceRepository', $params[1]->getName());
        $this->assertEquals('eventDispatcher', $params[2]->getName());
        $this->assertEquals('security', $params[3]->getName());
    }
}
