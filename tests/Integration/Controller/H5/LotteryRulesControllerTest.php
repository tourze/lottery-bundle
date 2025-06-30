<?php

namespace LotteryBundle\Tests\Integration\Controller\H5;

use LotteryBundle\Controller\H5\LotteryRulesController;
use PHPUnit\Framework\TestCase;

class LotteryRulesControllerTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(LotteryRulesController::class));
    }
}