<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\ChanceEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(ChanceEvent::class)]
final class ChanceEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new ChanceEvent();

        $this->assertInstanceOf(ChanceEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new ChanceEvent();
        $chance = new Chance();

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }
}
