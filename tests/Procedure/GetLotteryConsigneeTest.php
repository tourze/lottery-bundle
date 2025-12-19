<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetLotteryConsignee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetLotteryConsignee::class)]
#[RunTestsInSeparateProcesses]
final class GetLotteryConsigneeTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(GetLotteryConsignee::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheKey'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheDuration'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheTags'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryConsignee::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testGetCacheKeyMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryConsignee::class, 'getCacheKey');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    public function testGetCacheDurationMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryConsignee::class, 'getCacheDuration');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('int', $returnType->getName());
    }

    public function testGetCacheTagsMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryConsignee::class, 'getCacheTags');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('iterable', $returnType->getName());
    }
}
