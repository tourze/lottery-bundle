<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\ChanceLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryChanceLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ChanceLog::class;
    }
}
