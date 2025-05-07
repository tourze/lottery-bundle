<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Prize;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryPrizeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prize::class;
    }
}
