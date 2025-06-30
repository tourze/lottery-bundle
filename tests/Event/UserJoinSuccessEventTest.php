<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\UserJoinSuccessEvent;
use PHPUnit\Framework\TestCase;
use Tourze\UserEventBundle\Event\UserInteractionContext;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

class UserJoinSuccessEventTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $event = new UserJoinSuccessEvent();

        $this->assertInstanceOf(UserJoinSuccessEvent::class, $event);
        $this->assertInstanceOf(UserInteractionEvent::class, $event);
        $this->assertInstanceOf(UserInteractionContext::class, $event);
    }

    public function test_setChance_setsAndGetsChance(): void
    {
        $event = new UserJoinSuccessEvent();
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function test_setActivity_setsAndGetsActivity(): void
    {
        $event = new UserJoinSuccessEvent();
        $activity = $this->createMock(Activity::class);

        $event->setActivity($activity);

        $this->assertSame($activity, $event->getActivity());
    }



    public function test_getContext_returnsArrayWithChanceAndActivity(): void
    {
        $event = new UserJoinSuccessEvent();
        $chance = $this->createMock(Chance::class);
        $activity = $this->createMock(Activity::class);

        $event->setChance($chance);
        $event->setActivity($activity);

        $context = $event->getContext();

        $this->assertArrayHasKey('chance', $context);
        $this->assertArrayHasKey('activity', $context);
        $this->assertSame($chance, $context['chance']);
        $this->assertSame($activity, $context['activity']);
    }

    public function test_fullWorkflow_setAndGetAllProperties(): void
    {
        $event = new UserJoinSuccessEvent();
        $chance = $this->createMock(Chance::class);
        $activity = $this->createMock(Activity::class);

        $event->setChance($chance);
        $event->setActivity($activity);

        $this->assertSame($chance, $event->getChance());
        $this->assertSame($activity, $event->getActivity());
        
        $context = $event->getContext();
        $this->assertSame($chance, $context['chance']);
        $this->assertSame($activity, $context['activity']);
    }
} 