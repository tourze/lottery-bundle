<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Chance;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryChanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chance::class;
    }
}
