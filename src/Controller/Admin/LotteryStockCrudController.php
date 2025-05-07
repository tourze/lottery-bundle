<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Stock;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryStockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }
}
