<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\GetLotteryConsignee;
use PHPUnit\Framework\TestCase;

class GetLotteryConsigneeTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(GetLotteryConsignee::class));
    }
}