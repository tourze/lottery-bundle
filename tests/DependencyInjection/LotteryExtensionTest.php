<?php

namespace LotteryBundle\Tests\DependencyInjection;

use LotteryBundle\DependencyInjection\LotteryExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(LotteryExtension::class)]
final class LotteryExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $extension = new LotteryExtension();

        $this->assertInstanceOf(LotteryExtension::class, $extension);
        $this->assertInstanceOf(Extension::class, $extension);
    }

    public function testGetAliasReturnsCorrectAlias(): void
    {
        $extension = new LotteryExtension();

        $this->assertSame('lottery', $extension->getAlias());
    }

    public function testLoadMethodExists(): void
    {
        $extension = new LotteryExtension();

        // 使用反射验证 load 方法存在
        $reflection = new \ReflectionMethod($extension, 'load');
        $this->assertEquals('load', $reflection->getName());
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(2, $reflection->getNumberOfParameters());
    }

    public function testGetXsdValidationBasePathReturnsFalse(): void
    {
        $extension = new LotteryExtension();

        $result = $extension->getXsdValidationBasePath();
        $this->assertFalse($result);
    }

    public function testGetNamespaceReturnsHttp(): void
    {
        $extension = new LotteryExtension();

        $result = $extension->getNamespace();
        $this->assertSame('http://example.org/schema/dic/lottery', $result);
    }
}
