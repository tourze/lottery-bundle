<?php

namespace LotteryBundle\Tests\Procedure;

use LotteryBundle\Procedure\ServerSendLotteryChance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

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
        $this->assertEquals('array', $returnType->getName());
    }

    public function testHasRequiredProperties(): void
    {
        $reflectionClass = new \ReflectionClass(ServerSendLotteryChance::class);

        $this->assertTrue($reflectionClass->hasProperty('activityId'));
        $this->assertTrue($reflectionClass->hasProperty('userIdentity'));
        $this->assertTrue($reflectionClass->hasProperty('title'));
        $this->assertTrue($reflectionClass->hasProperty('startTime'));
        $this->assertTrue($reflectionClass->hasProperty('expireTime'));

        $activityIdProperty = $reflectionClass->getProperty('activityId');
        $this->assertTrue($activityIdProperty->isPublic());
        $activityIdType = $activityIdProperty->getType();
        if ($activityIdType instanceof \ReflectionNamedType) {
            $this->assertEquals('int', $activityIdType->getName());
        }

        $userIdentityProperty = $reflectionClass->getProperty('userIdentity');
        $this->assertTrue($userIdentityProperty->isPublic());
        $userIdentityType = $userIdentityProperty->getType();
        if ($userIdentityType instanceof \ReflectionNamedType) {
            $this->assertEquals('string', $userIdentityType->getName());
        }
    }
}
