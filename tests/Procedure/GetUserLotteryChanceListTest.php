<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetUserLotteryChanceList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

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

    public function testServiceExists(): void
    {
        $procedure = self::getService(GetUserLotteryChanceList::class);
        $this->assertInstanceOf(GetUserLotteryChanceList::class, $procedure);
    }

    public function testExtendsBaseProcedure(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserLotteryChanceList::class);
        $this->assertTrue($reflectionClass->isSubclassOf(BaseProcedure::class));
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
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserLotteryChanceList::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(3, $params);

        $this->assertEquals('activityRepository', $params[0]->getName());
        $this->assertEquals('security', $params[1]->getName());
        $this->assertEquals('chanceRepository', $params[2]->getName());
    }
}
