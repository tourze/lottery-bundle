<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Event\DecidePoolEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DecidePoolEventTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $event = new DecidePoolEvent();

        $this->assertInstanceOf(DecidePoolEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_constructor_setsDefaultNullValues(): void
    {
        $event = new DecidePoolEvent();

        $this->assertNull($event->getChance());
        $this->assertNull($event->getPool());
        $this->assertNull($event->getUser());
        $this->assertNull($event->getActivity());
    }

    public function test_setChance_setsAndGetsChance(): void
    {
        $event = new DecidePoolEvent();
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function test_setPool_setsAndGetsPool(): void
    {
        $event = new DecidePoolEvent();
        $pool = $this->createMock(Pool::class);

        $event->setPool($pool);

        $this->assertSame($pool, $event->getPool());
    }

    public function test_setUser_setsAndGetsUser(): void
    {
        $event = new DecidePoolEvent();
        $user = $this->createMock(UserInterface::class);

        $event->setUser($user);

        $this->assertSame($user, $event->getUser());
    }

    public function test_setActivity_setsAndGetsActivity(): void
    {
        $event = new DecidePoolEvent();
        $activity = $this->createMock(Activity::class);

        $event->setActivity($activity);

        $this->assertSame($activity, $event->getActivity());
    }

    public function test_setNullValues_setsAllToNull(): void
    {
        $event = new DecidePoolEvent();
        
        // 先设置一些非null值
        $event->setChance($this->createMock(Chance::class));
        $event->setPool($this->createMock(Pool::class));
        $event->setUser($this->createMock(UserInterface::class));
        $event->setActivity($this->createMock(Activity::class));

        // 然后设置为null
        $event->setChance(null);
        $event->setPool(null);
        $event->setUser(null);
        $event->setActivity(null);

        $this->assertNull($event->getChance());
        $this->assertNull($event->getPool());
        $this->assertNull($event->getUser());
        $this->assertNull($event->getActivity());
    }

    public function test_fullWorkflow_setAndGetAllProperties(): void
    {
        $event = new DecidePoolEvent();
        $chance = $this->createMock(Chance::class);
        $pool = $this->createMock(Pool::class);
        $user = $this->createMock(UserInterface::class);
        $activity = $this->createMock(Activity::class);

        $event->setChance($chance);
        $event->setPool($pool);
        $event->setUser($user);
        $event->setActivity($activity);

        $this->assertSame($chance, $event->getChance());
        $this->assertSame($pool, $event->getPool());
        $this->assertSame($user, $event->getUser());
        $this->assertSame($activity, $event->getActivity());
    }
} 