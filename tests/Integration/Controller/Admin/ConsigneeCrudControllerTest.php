<?php

namespace LotteryBundle\Tests\Integration\Controller\Admin;

use LotteryBundle\Controller\Admin\ConsigneeCrudController;
use LotteryBundle\Entity\Consignee;
use PHPUnit\Framework\TestCase;

class ConsigneeCrudControllerTest extends TestCase
{
    public function test_getEntityFqcn_returnsConsigneeClass(): void
    {
        $controller = new ConsigneeCrudController();
        
        $this->assertEquals(Consignee::class, $controller::getEntityFqcn());
    }

    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $controller = new ConsigneeCrudController();
        
        $this->assertInstanceOf(ConsigneeCrudController::class, $controller);
    }
}