<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetLotteryPrizeList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetLotteryPrizeList::class)]
#[RunTestsInSeparateProcesses]
final class GetLotteryPrizeListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(GetLotteryPrizeList::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheKey'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheDuration'));
        $this->assertTrue($reflectionClass->hasMethod('getCacheTags'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetLotteryPrizeList::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    public function testHasRequiredProperties(): void
    {
        $reflectionClass = new \ReflectionClass(GetLotteryPrizeList::class);

        $this->assertTrue($reflectionClass->hasProperty('activityId'));

        $activityIdProperty = $reflectionClass->getProperty('activityId');
        $this->assertTrue($activityIdProperty->isPublic());
        $propertyType = $activityIdProperty->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $propertyType);
        $this->assertEquals('string', $propertyType->getName());
    }
}
