<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\GetUserValidLotteryChanceCounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetUserValidLotteryChanceCounts::class)]
#[RunTestsInSeparateProcesses]
final class GetUserValidLotteryChanceCountsTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testServiceExists(): void
    {
        $procedure = self::getService(GetUserValidLotteryChanceCounts::class);
        $this->assertInstanceOf(GetUserValidLotteryChanceCounts::class, $procedure);
    }

    public function testExtendsBaseProcedure(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserValidLotteryChanceCounts::class);
        $this->assertTrue($reflectionClass->isSubclassOf(BaseProcedure::class));
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserValidLotteryChanceCounts::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(GetUserValidLotteryChanceCounts::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(GetUserValidLotteryChanceCounts::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(3, $params);

        $this->assertEquals('activityRepository', $params[0]->getName());
        $this->assertEquals('security', $params[1]->getName());
        $this->assertEquals('chanceRepository', $params[2]->getName());
    }
}
