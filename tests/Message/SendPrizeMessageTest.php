<?php

namespace LotteryBundle\Tests\Message;

use LotteryBundle\Message\SendPrizeMessage;
use PHPUnit\Framework\TestCase;
use Tourze\AsyncContracts\AsyncMessageInterface;

class SendPrizeMessageTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $message = new SendPrizeMessage();

        $this->assertInstanceOf(SendPrizeMessage::class, $message);
        $this->assertInstanceOf(AsyncMessageInterface::class, $message);
    }

    public function test_setChanceId_setsAndGetsValue(): void
    {
        $message = new SendPrizeMessage();
        $chanceId = '123';

        $message->setChanceId($chanceId);

        $this->assertSame($chanceId, $message->getChanceId());
    }

    public function test_setChanceId_withNumericString_setsAndGetsValue(): void
    {
        $message = new SendPrizeMessage();
        $chanceId = '456';

        $message->setChanceId($chanceId);

        $this->assertSame($chanceId, $message->getChanceId());
    }
} 