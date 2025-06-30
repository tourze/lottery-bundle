<?php

namespace LotteryBundle\Tests\Integration\MessageHandler;

use LotteryBundle\MessageHandler\SendPrizeHandler;
use PHPUnit\Framework\TestCase;

class SendPrizeHandlerTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(SendPrizeHandler::class));
    }
}