<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\JoinLottery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(JoinLottery::class)]
#[RunTestsInSeparateProcesses]
final class JoinLotteryTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testServiceExists(): void
    {
        $procedure = self::getService(JoinLottery::class);
        $this->assertInstanceOf(JoinLottery::class, $procedure);
    }

    public function testExtendsLockableProcedure(): void
    {
        $reflectionClass = new \ReflectionClass(JoinLottery::class);
        $this->assertTrue($reflectionClass->isSubclassOf(LockableProcedure::class));
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(JoinLottery::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(JoinLottery::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(JoinLottery::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(8, $params);

        $this->assertEquals('activityRepository', $params[0]->getName());
        $this->assertEquals('chanceRepository', $params[1]->getName());
        $this->assertEquals('luckyService', $params[2]->getName());
        $this->assertEquals('logger', $params[3]->getName());
        $this->assertEquals('textFormatter', $params[4]->getName());
        $this->assertEquals('security', $params[5]->getName());
        $this->assertEquals('eventDispatcher', $params[6]->getName());
        $this->assertEquals('entityManager', $params[7]->getName());
    }
}
