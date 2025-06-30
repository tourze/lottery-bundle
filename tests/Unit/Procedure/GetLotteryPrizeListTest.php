<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\GetLotteryPrizeList;
use PHPUnit\Framework\TestCase;

class GetLotteryPrizeListTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(GetLotteryPrizeList::class));
    }
}