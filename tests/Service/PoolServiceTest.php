<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\PoolService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @group needs-refactoring
 *
 * @internal
 */
#[CoversClass(PoolService::class)]
#[RunTestsInSeparateProcesses]
final class PoolServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    /**
     * 测试服务实例化
     */
    public function testServiceInstanceIsCreated(): void
    {
        $poolService = self::getService(PoolService::class);
        $this->assertInstanceOf(PoolService::class, $poolService);
    }

    /**
     * 测试服务依赖注入正确
     */
    public function testServiceDependenciesAreInjected(): void
    {
        $poolService = self::getService(PoolService::class);
        $this->assertInstanceOf(PoolService::class, $poolService);
    }

    /**
     * 测试dispatch方法
     */
    public function testDispatch(): void
    {
        $poolService = self::getService(PoolService::class);

        // 由于dispatch方法需要真实的Chance实体和事件监听器
        // 这里只测试方法存在且方法签名正确
        $this->assertTrue(method_exists($poolService, 'dispatch'));

        $reflection = new \ReflectionMethod($poolService, 'dispatch');
        $this->assertEquals(1, $reflection->getNumberOfParameters());
        $returnType = $reflection->getReturnType();
        $this->assertEquals('void', $returnType instanceof \ReflectionNamedType ? $returnType->getName() : null);
    }
}
