<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Event\DecidePrizeProbabilityEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(DecidePrizeProbabilityEvent::class)]
final class DecidePrizeProbabilityEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new DecidePrizeProbabilityEvent();

        $this->assertInstanceOf(DecidePrizeProbabilityEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $chance = new Chance();

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function testSetPrizeSetsAndGetsPrize(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $prize = new Prize();
        $prize->setType('virtual');

        $event->setPrize($prize);

        $this->assertSame($prize, $event->getPrize());
    }

    public function testSetRateWithIntegerSetsAndGetsRate(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $rate = 50;

        $event->setRate($rate);

        $this->assertSame($rate, $event->getRate());
    }

    public function testSetRateWithFloatSetsAndGetsRate(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $rate = 75.5;

        $event->setRate($rate);

        $this->assertSame($rate, $event->getRate());
    }

    public function testFullWorkflowSetAndGetAllProperties(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $chance = new Chance();
        $prize = new Prize();
        $prize->setType('virtual');
        $rate = 85.7;

        $event->setChance($chance);
        $event->setPrize($prize);
        $event->setRate($rate);

        $this->assertSame($chance, $event->getChance());
        $this->assertSame($prize, $event->getPrize());
        $this->assertSame($rate, $event->getRate());
    }
}
