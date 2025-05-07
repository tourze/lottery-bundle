<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Consignee;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryConsigneeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Consignee::class;
    }
}
