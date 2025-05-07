<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Watchword;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryWatchwordCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Watchword::class;
    }
}
