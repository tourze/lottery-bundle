<?php

namespace LotteryBundle\Tests\Message;

use LotteryBundle\Message\SendPrizeMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\AsyncContracts\AsyncMessageInterface;

/**
 * @internal
 */
#[CoversClass(SendPrizeMessage::class)]
final class SendPrizeMessageTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $message = new SendPrizeMessage();

        $this->assertInstanceOf(SendPrizeMessage::class, $message);
        $this->assertInstanceOf(AsyncMessageInterface::class, $message);
    }

    public function testSetChanceIdSetsAndGetsValue(): void
    {
        $message = new SendPrizeMessage();
        $chanceId = '123';

        $message->setChanceId($chanceId);

        $this->assertSame($chanceId, $message->getChanceId());
    }

    public function testSetChanceIdWithNumericStringSetsAndGetsValue(): void
    {
        $message = new SendPrizeMessage();
        $chanceId = '456';

        $message->setChanceId($chanceId);

        $this->assertSame($chanceId, $message->getChanceId());
    }
}
