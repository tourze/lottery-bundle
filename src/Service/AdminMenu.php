<?php

namespace LotteryBundle\Service;

use Knp\Menu\ItemInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Entity\Pool;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 抽奖活动菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('抽奖活动') === null) {
            $item->addChild('抽奖活动')
                ->setAttribute('icon', 'fas fa-gift');
        }

        $lotteryMenu = $item->getChild('抽奖活动');

        // 活动管理菜单
        $lotteryMenu->addChild('活动管理')
            ->setUri($this->linkGenerator->getCurdListPage(Activity::class))
            ->setAttribute('icon', 'fas fa-calendar-alt');
            
        // 奖池管理菜单（包含奖品管理）
        $lotteryMenu->addChild('奖池管理')
            ->setUri($this->linkGenerator->getCurdListPage(Pool::class))
            ->setAttribute('icon', 'fas fa-box');
            
        // 机会管理菜单
        $lotteryMenu->addChild('机会管理')
            ->setUri($this->linkGenerator->getCurdListPage(Chance::class))
            ->setAttribute('icon', 'fas fa-dice');
            
        // 收货信息菜单
        $lotteryMenu->addChild('收货信息')
            ->setUri($this->linkGenerator->getCurdListPage(Consignee::class))
            ->setAttribute('icon', 'fas fa-shipping-fast');
    }
}
