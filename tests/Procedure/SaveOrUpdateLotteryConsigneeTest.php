<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\SaveOrUpdateLotteryConsignee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

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

    public function testServiceExists(): void
    {
        $procedure = self::getService(SaveOrUpdateLotteryConsignee::class);
        $this->assertInstanceOf(SaveOrUpdateLotteryConsignee::class, $procedure);
    }

    public function testExtendsLockableProcedure(): void
    {
        $reflectionClass = new \ReflectionClass(SaveOrUpdateLotteryConsignee::class);
        $this->assertTrue($reflectionClass->isSubclassOf(LockableProcedure::class));
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
        $this->assertEquals(ArrayResult::class, $returnType->getName());
    }

    public function testConstructorParameters(): void
    {
        $reflectionClass = new \ReflectionClass(SaveOrUpdateLotteryConsignee::class);
        $constructor = $reflectionClass->getConstructor();

        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $this->assertCount(4, $params);

        $this->assertEquals('chanceRepository', $params[0]->getName());
        $this->assertEquals('consigneeRepository', $params[1]->getName());
        $this->assertEquals('security', $params[2]->getName());
        $this->assertEquals('entityManager', $params[3]->getName());
    }
}
