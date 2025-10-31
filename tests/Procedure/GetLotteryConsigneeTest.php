<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetLotteryConsignee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

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
        $this->assertEquals('array', ($returnType = $reflectionMethod->getReturnType()) instanceof \ReflectionNamedType ? $returnType->getName() : 'unknown');
    }

    public function testHasRequiredProperties(): void
    {
        $reflectionClass = new \ReflectionClass(GetLotteryConsignee::class);

        $this->assertTrue($reflectionClass->hasProperty('chanceId'));

        $chanceIdProperty = $reflectionClass->getProperty('chanceId');
        $this->assertTrue($chanceIdProperty->isPublic());
        $propertyType = $chanceIdProperty->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $propertyType);
        $this->assertEquals('int', $propertyType->getName());
    }
}
