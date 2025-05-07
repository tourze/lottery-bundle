<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\WatchwordLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryWatchwordLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WatchwordLog::class;
    }
}
