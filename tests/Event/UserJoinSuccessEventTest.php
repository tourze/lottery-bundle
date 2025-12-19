<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\UserJoinSuccessEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use Tourze\UserEventBundle\Event\UserInteractionContext;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

/**
 * @internal
 */
#[CoversClass(UserJoinSuccessEvent::class)]
final class UserJoinSuccessEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new UserJoinSuccessEvent();

        $this->assertInstanceOf(UserJoinSuccessEvent::class, $event);
        $this->assertInstanceOf(UserInteractionEvent::class, $event);
        $this->assertInstanceOf(UserInteractionContext::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new UserJoinSuccessEvent();
        $chance = new Chance();

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function testSetActivitySetsAndGetsActivity(): void
    {
        $event = new UserJoinSuccessEvent();
        $activity = new Activity();

        $event->setActivity($activity);

        $this->assertSame($activity, $event->getActivity());
    }

    public function testGetContextReturnsArrayWithChanceAndActivity(): void
    {
        $event = new UserJoinSuccessEvent();
        $chance = new Chance();
        $activity = new Activity();

        $event->setChance($chance);
        $event->setActivity($activity);

        $context = $event->getContext();

        $this->assertArrayHasKey('chance', $context);
        $this->assertArrayHasKey('activity', $context);
        $this->assertSame($chance, $context['chance']);
        $this->assertSame($activity, $context['activity']);
    }

    public function testFullWorkflowSetAndGetAllProperties(): void
    {
        $event = new UserJoinSuccessEvent();
        $chance = new Chance();
        $activity = new Activity();

        $event->setChance($chance);
        $event->setActivity($activity);

        $this->assertSame($chance, $event->getChance());
        $this->assertSame($activity, $event->getActivity());

        $context = $event->getContext();
        $this->assertSame($chance, $context['chance']);
        $this->assertSame($activity, $context['activity']);
    }
}
