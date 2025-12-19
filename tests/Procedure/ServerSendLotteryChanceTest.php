<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\ServerSendLotteryChance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(ServerSendLotteryChance::class)]
#[RunTestsInSeparateProcesses]
final class ServerSendLotteryChanceTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 子类特定的初始化逻辑
    }

    public function testServiceExists(): void
    {
        $procedure = self::getService(ServerSendLotteryChance::class);
        $this->assertInstanceOf(ServerSendLotteryChance::class, $procedure);
    }

    public function testExtendsLockableProcedure(): void
    {
        $reflectionClass = new \ReflectionClass(ServerSendLotteryChance::class);
        $this->assertTrue($reflectionClass->isSubclassOf(LockableProcedure::class));
    }

    public function testHasRequiredMethods(): void
    {
        $reflectionClass = new \ReflectionClass(ServerSendLotteryChance::class);

        $this->assertTrue($reflectionClass->hasMethod('execute'));
    }

    public function testExecuteMethodSignature(): void
    {
        $reflectionMethod = new \ReflectionMethod(ServerSendLotteryChance::class, 'execute');

        $this->assertTrue($reflectionMethod->isPublic());
        $returnType = $reflectionMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(ServerSendLotteryChance::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(3, $params);

        $this->assertEquals('activityRepository', $params[0]->getName());
        $this->assertEquals('lotteryService', $params[1]->getName());
        $this->assertEquals('userLoader', $params[2]->getName());
    }
}
