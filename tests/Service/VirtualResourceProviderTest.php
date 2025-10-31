<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\VirtualResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\ResourceManageBundle\Service\ResourceProvider;

/**
 * @internal
 */
#[CoversClass(VirtualResourceProvider::class)]
final class VirtualResourceProviderTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertInstanceOf(VirtualResourceProvider::class, $provider);
        $this->assertInstanceOf(ResourceProvider::class, $provider);
    }

    public function testGetCodeReturnsCorrectValue(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertSame('virtual', $provider->getCode());
    }

    public function testGetLabelReturnsCorrectValue(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertSame('虚拟', $provider->getLabel());
    }

    public function testGetIdentitiesReturnsNull(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertNull($provider->getIdentities());
    }

    public function testFindIdentityReturnsNull(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertNull($provider->findIdentity('test'));
    }

    public function testSendResourceDoesNotThrow(): void
    {
        $provider = new VirtualResourceProvider();
        $user = $this->createMock(UserInterface::class);

        // 此测试验证方法执行不抛出异常，无需额外断言
        $this->expectNotToPerformAssertions();
        $provider->sendResource($user, null, '100');
    }
}
