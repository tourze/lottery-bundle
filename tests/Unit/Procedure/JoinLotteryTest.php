<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\JoinLottery;
use PHPUnit\Framework\TestCase;

class JoinLotteryTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(JoinLottery::class));
    }
}