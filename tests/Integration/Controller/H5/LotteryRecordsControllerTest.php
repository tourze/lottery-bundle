<?php

namespace LotteryBundle\Tests\Integration\Controller\H5;

use LotteryBundle\Controller\H5\LotteryRecordsController;
use PHPUnit\Framework\TestCase;

class LotteryRecordsControllerTest extends TestCase
{
    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $controller = new LotteryRecordsController();
        
        $this->assertInstanceOf(LotteryRecordsController::class, $controller);
    }
}