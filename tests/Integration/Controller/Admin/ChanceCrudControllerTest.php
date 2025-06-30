<?php

namespace LotteryBundle\Tests\Integration\Controller\Admin;

use LotteryBundle\Controller\Admin\ChanceCrudController;
use LotteryBundle\Entity\Chance;
use PHPUnit\Framework\TestCase;

class ChanceCrudControllerTest extends TestCase
{
    public function test_getEntityFqcn_returnsChanceClass(): void
    {
        $controller = new ChanceCrudController();
        
        $this->assertEquals(Chance::class, $controller::getEntityFqcn());
    }

    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $controller = new ChanceCrudController();
        
        $this->assertInstanceOf(ChanceCrudController::class, $controller);
    }
}