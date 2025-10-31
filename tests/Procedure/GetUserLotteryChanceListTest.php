<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetUserLotteryChanceList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetUserLotteryChanceList::class)]
#[RunTestsInSeparateProcesses]
final class GetUserLotteryChanceListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserLotteryChanceList::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetUserLotteryChanceList::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    public function testHasRequiredProperties(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserLotteryChanceList::class);

        $this->assertTrue($reflectionClass->hasProperty('activityId'));

        $activityIdProperty = $reflectionClass->getProperty('activityId');
        $this->assertTrue($activityIdProperty->isPublic());
        $propertyType = $activityIdProperty->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $propertyType);
        $this->assertEquals('int', $propertyType->getName());
    }
}
