<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\VirtualResourceProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\ResourceManageBundle\Service\ResourceProvider;

class VirtualResourceProviderTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertInstanceOf(VirtualResourceProvider::class, $provider);
        $this->assertInstanceOf(ResourceProvider::class, $provider);
    }

    public function test_getCode_returnsCorrectValue(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertSame('virtual', $provider->getCode());
    }

    public function test_getLabel_returnsCorrectValue(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertSame('虚拟', $provider->getLabel());
    }

    public function test_getIdentities_returnsNull(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertNull($provider->getIdentities());
    }

    public function test_findIdentity_returnsNull(): void
    {
        $provider = new VirtualResourceProvider();

        $this->assertNull($provider->findIdentity('test'));
    }

    public function test_sendResource_doesNotThrow(): void
    {
        $provider = new VirtualResourceProvider();
        $user = $this->createMock(UserInterface::class);

        $provider->sendResource($user, null, '100');

        $this->assertTrue(true); // 确保没有抛出异常
    }
} 