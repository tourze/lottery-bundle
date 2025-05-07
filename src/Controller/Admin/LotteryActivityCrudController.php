<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\Activity;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryActivityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Activity::class;
    }
}
