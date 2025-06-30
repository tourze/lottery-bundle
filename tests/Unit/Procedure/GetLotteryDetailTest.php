<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\GetLotteryDetail;
use PHPUnit\Framework\TestCase;

class GetLotteryDetailTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(GetLotteryDetail::class));
    }
}