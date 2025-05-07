<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Pool;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryPoolCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pool::class;
    }
}
