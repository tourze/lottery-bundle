<?php

namespace LotteryBundle\Tests\Integration\Controller\Admin;

use LotteryBundle\Controller\Admin\ActivityCrudController;
use LotteryBundle\Entity\Activity;
use PHPUnit\Framework\TestCase;

class ActivityCrudControllerTest extends TestCase
{
    public function test_getEntityFqcn_returnsActivityClass(): void
    {
        $controller = new ActivityCrudController();
        
        $this->assertEquals(Activity::class, $controller::getEntityFqcn());
    }

    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $controller = new ActivityCrudController();
        
        $this->assertInstanceOf(ActivityCrudController::class, $controller);
    }
}