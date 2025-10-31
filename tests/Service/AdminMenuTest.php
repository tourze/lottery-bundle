<?php

namespace LotteryBundle\Tests\Service;

use LotteryBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Menu 测试不需要特殊的设置逻辑
    }

    public function testServiceCanBeRetrievedFromContainer(): void
    {
        $menu = self::getService(AdminMenu::class);

        $this->assertInstanceOf(AdminMenu::class, $menu);
        $this->assertInstanceOf(MenuProviderInterface::class, $menu);
    }

    public function testServiceHasRequiredDependency(): void
    {
        // 测试服务可以被正常获取，说明依赖注入正确
        $menu = self::getService(AdminMenu::class);

        // 验证服务可以正常工作，不测试具体逻辑
        $this->assertInstanceOf(AdminMenu::class, $menu);
    }
}
