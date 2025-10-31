<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\TextResourceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\ResourceManageBundle\Service\ResourceProvider;

/**
 * @internal
 */
#[CoversClass(TextResourceProvider::class)]
final class TextResourceProviderTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $provider = new TextResourceProvider();

        $this->assertInstanceOf(TextResourceProvider::class, $provider);
        $this->assertInstanceOf(ResourceProvider::class, $provider);
    }

    public function testGetCodeReturnsCorrectValue(): void
    {
        $provider = new TextResourceProvider();

        $this->assertSame('text', $provider->getCode());
    }

    public function testGetLabelReturnsCorrectValue(): void
    {
        $provider = new TextResourceProvider();

        $this->assertSame('文本(安慰奖)', $provider->getLabel());
    }

    public function testGetIdentitiesReturnsNull(): void
    {
        $provider = new TextResourceProvider();

        $this->assertNull($provider->getIdentities());
    }

    public function testFindIdentityReturnsNull(): void
    {
        $provider = new TextResourceProvider();

        $this->assertNull($provider->findIdentity('test'));
    }

    public function testSendResourceDoesNotThrow(): void
    {
        $provider = new TextResourceProvider();
        $user = $this->createMock(UserInterface::class);

        // 此测试验证方法执行不抛出异常，无需额外断言
        $this->expectNotToPerformAssertions();
        $provider->sendResource($user, null, '100');
    }
}
