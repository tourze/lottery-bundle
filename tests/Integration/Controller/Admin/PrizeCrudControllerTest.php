<?php

namespace LotteryBundle\Tests\Integration\Controller\Admin;

use LotteryBundle\Controller\Admin\PrizeCrudController;
use LotteryBundle\Entity\Prize;
use PHPUnit\Framework\TestCase;

class PrizeCrudControllerTest extends TestCase
{
    public function test_getEntityFqcn_returnsPrizeClass(): void
    {
        $this->assertEquals(Prize::class, PrizeCrudController::getEntityFqcn());
    }

    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(PrizeCrudController::class));
    }
}