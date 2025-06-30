<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\GetUserValidLotteryChanceCounts;
use PHPUnit\Framework\TestCase;

class GetUserValidLotteryChanceCountsTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(GetUserValidLotteryChanceCounts::class));
    }
}