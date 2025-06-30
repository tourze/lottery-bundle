<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\GetUserLotteryChanceList;
use PHPUnit\Framework\TestCase;

class GetUserLotteryChanceListTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(GetUserLotteryChanceList::class));
    }
}