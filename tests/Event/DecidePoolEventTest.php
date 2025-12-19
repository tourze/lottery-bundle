<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Event\DecidePoolEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(DecidePoolEvent::class)]
final class DecidePoolEventTest extends AbstractEventTestCase
{

    public function testConstructorCreatesInstance(): void
    {
        $event = new DecidePoolEvent();

        $this->assertInstanceOf(DecidePoolEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testConstructorSetsDefaultNullValues(): void
    {
        $event = new DecidePoolEvent();

        $this->assertNull($event->getChance());
        $this->assertNull($event->getPool());
        $this->assertNull($event->getUser());
        $this->assertNull($event->getActivity());
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new DecidePoolEvent();
        $chance = new Chance();

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function testSetPoolSetsAndGetsPool(): void
    {
        $event = new DecidePoolEvent();
        $pool = new Pool();

        $event->setPool($pool);

        $this->assertSame($pool, $event->getPool());
    }

    public function testSetUserSetsAndGetsUser(): void
    {
        $event = new DecidePoolEvent();
        $user = new InMemoryUser('test_user', 'password');

        $event->setUser($user);

        $this->assertSame($user, $event->getUser());
    }

    public function testSetActivitySetsAndGetsActivity(): void
    {
        $event = new DecidePoolEvent();
        $activity = new Activity();

        $event->setActivity($activity);

        $this->assertSame($activity, $event->getActivity());
    }

    public function testSetNullValuesSetsAllToNull(): void
    {
        $event = new DecidePoolEvent();

        // 先设置一些非null值
        $event->setChance(new Chance());
        $event->setPool(new Pool());
        $event->setUser(new InMemoryUser('test_user', 'password'));
        $event->setActivity(new Activity());

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

    public function testFullWorkflowSetAndGetAllProperties(): void
    {
        $event = new DecidePoolEvent();
        $chance = new Chance();
        $pool = new Pool();
        $user = new InMemoryUser('test_user', 'password');
        $activity = new Activity();

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
