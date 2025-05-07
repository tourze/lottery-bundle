<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\PoolAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryPoolAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PoolAttribute::class;
    }
}
