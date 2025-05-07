<?php

namespace LotteryBundle\Controller\Admin;

use LotteryBundle\Entity\ActivityAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class LotteryActivityAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActivityAttribute::class;
    }
}
