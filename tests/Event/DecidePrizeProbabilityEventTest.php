<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Event\DecidePrizeProbabilityEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;

class DecidePrizeProbabilityEventTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $event = new DecidePrizeProbabilityEvent();

        $this->assertInstanceOf(DecidePrizeProbabilityEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_setChance_setsAndGetsChance(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function test_setPrize_setsAndGetsPrize(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $prize = $this->createMock(Prize::class);

        $event->setPrize($prize);

        $this->assertSame($prize, $event->getPrize());
    }

    public function test_setRate_withInteger_setsAndGetsRate(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $rate = 50;

        $event->setRate($rate);

        $this->assertSame($rate, $event->getRate());
    }

    public function test_setRate_withFloat_setsAndGetsRate(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $rate = 75.5;

        $event->setRate($rate);

        $this->assertSame($rate, $event->getRate());
    }




    public function test_fullWorkflow_setAndGetAllProperties(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $chance = $this->createMock(Chance::class);
        $prize = $this->createMock(Prize::class);
        $rate = 85.7;

        $event->setChance($chance);
        $event->setPrize($prize);
        $event->setRate($rate);

        $this->assertSame($chance, $event->getChance());
        $this->assertSame($prize, $event->getPrize());
        $this->assertSame($rate, $event->getRate());
    }
} 