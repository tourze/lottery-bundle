<?php

namespace LotteryBundle\Tests\Integration\Controller\H5;

use LotteryBundle\Controller\H5\LotteryIndexController;
use PHPUnit\Framework\TestCase;

class LotteryIndexControllerTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(LotteryIndexController::class));
    }
}