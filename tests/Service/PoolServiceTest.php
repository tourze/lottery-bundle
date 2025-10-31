<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Service\PoolService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @group needs-refactoring
 *
 * @internal
 */
#[CoversClass(PoolService::class)]
final class PoolServiceTest extends TestCase
{
    private EventDispatcherInterface|MockObject $eventDispatcher;

    private PoolService $poolService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->poolService = new PoolService(
            $this->eventDispatcher
        );
    }

    /**
     * 测试服务实例化
     */
    public function testServiceInstanceIsCreated(): void
    {
        // 验证服务可以正确实例化
        $this->assertInstanceOf(PoolService::class, $this->poolService);
    }

    /**
     * 测试服务依赖注入正确
     */
    public function testServiceDependenciesAreInjected(): void
    {
        // 验证所有依赖都已正确注入
        $reflection = new \ReflectionClass($this->poolService);

        $eventDispatcherProperty = $reflection->getProperty('eventDispatcher');
        $eventDispatcherProperty->setAccessible(true);
        $this->assertSame($this->eventDispatcher, $eventDispatcherProperty->getValue($this->poolService));
    }

    /**
     * 测试dispatch方法
     */
    public function testDispatch(): void
    {
        // 测试方法存在且方法签名正确
        $this->assertTrue((new \ReflectionClass($this->poolService))->hasMethod('dispatch'));

        $reflection = new \ReflectionMethod($this->poolService, 'dispatch');
        $this->assertEquals(1, $reflection->getNumberOfParameters());
        $returnType = $reflection->getReturnType();
        $this->assertEquals('void', $returnType instanceof \ReflectionNamedType ? $returnType->getName() : null);

        // 验证eventDispatcher依赖存在
        $this->assertInstanceOf(EventDispatcherInterface::class, $this->eventDispatcher);
    }
}
