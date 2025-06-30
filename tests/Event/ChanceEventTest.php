<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\ChanceEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;

class ChanceEventTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $event = new ChanceEvent();

        $this->assertInstanceOf(ChanceEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_setChance_setsAndGetsChance(): void
    {
        $event = new ChanceEvent();
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

} 