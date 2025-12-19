<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\AfterChanceExpireEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(AfterChanceExpireEvent::class)]
final class AfterChanceExpireEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new AfterChanceExpireEvent();

        $this->assertInstanceOf(AfterChanceExpireEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new AfterChanceExpireEvent();
        $chance = new Chance();

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }
}
