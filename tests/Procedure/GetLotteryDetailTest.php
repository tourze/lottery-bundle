<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetLotteryDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetLotteryDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetLotteryDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(GetLotteryDetail::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheKey'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheDuration'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheTags'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryDetail::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testGetCacheKeyMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryDetail::class, 'getCacheKey');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    public function testGetCacheDurationMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryDetail::class, 'getCacheDuration');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('int', $returnType->getName());
    }

    public function testGetCacheTagsMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryDetail::class, 'getCacheTags');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('iterable', $returnType->getName());
    }
}
