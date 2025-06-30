<?php

namespace LotteryBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use LotteryBundle\Service\AdminMenu;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenuTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $menu = new AdminMenu($linkGenerator);

        $this->assertInstanceOf(AdminMenu::class, $menu);
        $this->assertInstanceOf(MenuProviderInterface::class, $menu);
    }

    public function test_invoke_addsLotteryMenuItems(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->expects($this->exactly(4))
            ->method('getCurdListPage')
            ->willReturn('/admin/test');

        $childMenu = $this->createMock(ItemInterface::class);
        $childMenu->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnSelf();

        $childMenu->expects($this->exactly(4))
            ->method('setUri')
            ->with('/admin/test')
            ->willReturnSelf();

        $childMenu->expects($this->exactly(5))
            ->method('setAttribute')
            ->willReturnSelf();

        $rootMenu = $this->createMock(ItemInterface::class);
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('抽奖活动')
            ->willReturnOnConsecutiveCalls(null, $childMenu);

        $rootMenu->expects($this->once())
            ->method('addChild')
            ->with('抽奖活动')
            ->willReturn($childMenu);

        $menu = new AdminMenu($linkGenerator);
        $menu($rootMenu);
    }

    public function test_invoke_withExistingMenu_usesExistingMenu(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->expects($this->exactly(4))
            ->method('getCurdListPage')
            ->willReturn('/admin/test');

        $existingMenu = $this->createMock(ItemInterface::class);
        $existingMenu->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnSelf();

        $existingMenu->expects($this->exactly(4))
            ->method('setUri')
            ->willReturnSelf();

        $existingMenu->expects($this->exactly(4))
            ->method('setAttribute')
            ->willReturnSelf();

        $rootMenu = $this->createMock(ItemInterface::class);
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('抽奖活动')
            ->willReturn($existingMenu);

        $rootMenu->expects($this->never())
            ->method('addChild');

        $menu = new AdminMenu($linkGenerator);
        $menu($rootMenu);
    }
} 