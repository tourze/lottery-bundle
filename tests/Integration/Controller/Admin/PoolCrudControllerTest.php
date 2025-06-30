<?php

namespace LotteryBundle\Tests\Integration\Controller\Admin;

use LotteryBundle\Controller\Admin\PoolCrudController;
use LotteryBundle\Entity\Pool;
use PHPUnit\Framework\TestCase;

class PoolCrudControllerTest extends TestCase
{
    public function test_getEntityFqcn_returnsPoolClass(): void
    {
        $this->assertEquals(Pool::class, PoolCrudController::getEntityFqcn());
    }

    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(PoolCrudController::class));
    }
}