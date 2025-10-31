<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\SaveOrUpdateLotteryConsignee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(SaveOrUpdateLotteryConsignee::class)]
#[RunTestsInSeparateProcesses]
final class SaveOrUpdateLotteryConsigneeTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(SaveOrUpdateLotteryConsignee::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(SaveOrUpdateLotteryConsignee::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    public function testHasRequiredProperties(): void
    {
        $reflectionClass = new \ReflectionClass(SaveOrUpdateLotteryConsignee::class);

        $this->assertTrue($reflectionClass->hasProperty('chanceId'));
        $this->assertTrue($reflectionClass->hasProperty('consigneeId'));
        $this->assertTrue($reflectionClass->hasProperty('realName'));
        $this->assertTrue($reflectionClass->hasProperty('mobile'));
        $this->assertTrue($reflectionClass->hasProperty('address'));

        $chanceIdProperty = $reflectionClass->getProperty('chanceId');
        $this->assertTrue($chanceIdProperty->isPublic());
        $propertyType = $chanceIdProperty->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $propertyType);
        $this->assertEquals('int', $propertyType->getName());

        $realNameProperty = $reflectionClass->getProperty('realName');
        $this->assertTrue($realNameProperty->isPublic());
        $propertyType = $realNameProperty->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $propertyType);
        $this->assertEquals('string', $propertyType->getName());
    }
}
