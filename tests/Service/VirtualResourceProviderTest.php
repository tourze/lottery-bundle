<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\VirtualResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\ResourceManageBundle\Service\ResourceProvider;

/**
 * @internal
 */
#[CoversClass(VirtualResourceProvider::class)]
#[RunTestsInSeparateProcesses]
final class VirtualResourceProviderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 初始化逻辑
    }

    public function testConstructorCreatesInstance(): void
    {
        $provider = self::getService(VirtualResourceProvider::class);

        $this->assertInstanceOf(VirtualResourceProvider::class, $provider);
        $this->assertInstanceOf(ResourceProvider::class, $provider);
    }

    public function testGetCodeReturnsCorrectValue(): void
    {
        $provider = self::getService(VirtualResourceProvider::class);

        $this->assertSame('virtual', $provider->getCode());
    }

    public function testGetLabelReturnsCorrectValue(): void
    {
        $provider = self::getService(VirtualResourceProvider::class);

        $this->assertSame('虚拟', $provider->getLabel());
    }

    public function testGetIdentitiesReturnsNull(): void
    {
        $provider = self::getService(VirtualResourceProvider::class);

        $this->assertNull($provider->getIdentities());
    }

    public function testFindIdentityReturnsNull(): void
    {
        $provider = self::getService(VirtualResourceProvider::class);

        $this->assertNull($provider->findIdentity('test'));
    }

    public function testSendResourceDoesNotThrow(): void
    {
        $provider = self::getService(VirtualResourceProvider::class);
        // 创建一个简单的用户实现用于测试
        $user = new class implements UserInterface {
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'test-user'; }
        };

        // 此测试验证方法执行不抛出异常，无需额外断言
        $this->expectNotToPerformAssertions();
        $provider->sendResource($user, null, '100');
    }
}
