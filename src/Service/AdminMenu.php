<?php

namespace LotteryBundle\Service;

use Knp\Menu\ItemInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('抽奖活动')) {
            $item->addChild('抽奖活动');
        }

        $item->getChild('抽奖活动')->addChild('活动管理')->setUri($this->linkGenerator->getCurdListPage(Activity::class));
        $item->getChild('抽奖活动')->addChild('奖池管理')->setUri($this->linkGenerator->getCurdListPage(Pool::class));
        $item->getChild('抽奖活动')->addChild('机会管理')->setUri($this->linkGenerator->getCurdListPage(Chance::class));
    }
}
