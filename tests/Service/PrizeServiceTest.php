<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\PrizeService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @group needs-refactoring
 *
 * @internal
 */
#[CoversClass(PrizeService::class)]
#[RunTestsInSeparateProcesses]
final class PrizeServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    /**
     * 测试服务实例化
     */
    public function testServiceInstanceIsCreated(): void
    {
        $prizeService = self::getService(PrizeService::class);
        $this->assertInstanceOf(PrizeService::class, $prizeService);
    }

    /**
     * 测试dispatch方法
     */
    public function testDispatch(): void
    {
        $prizeService = self::getService(PrizeService::class);

        // 测试方法存在且方法签名正确
        $this->assertTrue(method_exists($prizeService, 'dispatch'));

        $reflection = new \ReflectionMethod($prizeService, 'dispatch');
        $this->assertEquals(1, $reflection->getNumberOfParameters());
        $returnType = $reflection->getReturnType();
        $this->assertEquals('void', $returnType instanceof \ReflectionNamedType ? $returnType->getName() : null);
    }

    /**
     * 测试sendPrize方法
     */
    public function testSendPrize(): void
    {
        $prizeService = self::getService(PrizeService::class);

        // 测试方法存在且方法签名正确
        $this->assertTrue(method_exists($prizeService, 'sendPrize'));

        $reflection = new \ReflectionMethod($prizeService, 'sendPrize');
        $this->assertEquals(1, $reflection->getNumberOfParameters());
        $returnType = $reflection->getReturnType();
        $this->assertEquals('void', $returnType instanceof \ReflectionNamedType ? $returnType->getName() : null);
    }
}
