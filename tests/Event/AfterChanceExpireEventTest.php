<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\AfterChanceExpireEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;

class AfterChanceExpireEventTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $event = new AfterChanceExpireEvent();

        $this->assertInstanceOf(AfterChanceExpireEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_setChance_setsAndGetsChance(): void
    {
        $event = new AfterChanceExpireEvent();
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

} 