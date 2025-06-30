<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\ServerSendLotteryChance;
use PHPUnit\Framework\TestCase;

class ServerSendLotteryChanceTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(ServerSendLotteryChance::class));
    }
}