<?php

namespace LotteryBundle\Tests\DependencyInjection;

use LotteryBundle\DependencyInjection\LotteryExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class LotteryExtensionTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $extension = new LotteryExtension();

        $this->assertInstanceOf(LotteryExtension::class, $extension);
        $this->assertInstanceOf(Extension::class, $extension);
    }

    public function test_load_withEmptyConfig_loadsSuccessfully(): void
    {
        $extension = new LotteryExtension();
        $container = new ContainerBuilder();

        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('LotteryBundle\Service\AdminMenu'));
    }

    public function test_getAlias_returnsCorrectAlias(): void
    {
        $extension = new LotteryExtension();

        $this->assertSame('lottery', $extension->getAlias());
    }
} 