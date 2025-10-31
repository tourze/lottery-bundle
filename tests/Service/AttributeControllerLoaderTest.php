<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testInstanceIsInstanceOfExpectedClass(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testAutoload(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $result = $service->autoload();
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testLoad(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $result = $service->load('test-resource');
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupports(): void
    {
        $service = self::getService(AttributeControllerLoader::class);

        $result = $service->supports('test-resource');
        $this->assertFalse($result);
    }
}
