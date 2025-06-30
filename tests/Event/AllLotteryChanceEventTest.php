<?php

namespace LotteryBundle\Tests\Event;

use Doctrine\ORM\QueryBuilder;
use LotteryBundle\Event\AllLotteryChanceEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AllLotteryChanceEventTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $event = new AllLotteryChanceEvent();

        $this->assertInstanceOf(AllLotteryChanceEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_setQueryBuilder_setsAndGetsQueryBuilder(): void
    {
        $event = new AllLotteryChanceEvent();
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $event->setQueryBuilder($queryBuilder);

        $this->assertSame($queryBuilder, $event->getQueryBuilder());
    }

    public function test_setUser_setsAndGetsUser(): void
    {
        $event = new AllLotteryChanceEvent();
        $user = $this->createMock(UserInterface::class);

        $event->setUser($user);

        $this->assertSame($user, $event->getUser());
    }

    public function test_setActivityId_setsAndGetsActivityId(): void
    {
        $event = new AllLotteryChanceEvent();
        $activityId = 'activity123';

        $event->setActivityId($activityId);

        $this->assertSame($activityId, $event->getActivityId());
    }




    public function test_fullWorkflow_setAndGetAllProperties(): void
    {
        $event = new AllLotteryChanceEvent();
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $user = $this->createMock(UserInterface::class);
        $activityId = 'activity456';

        $event->setQueryBuilder($queryBuilder);
        $event->setUser($user);
        $event->setActivityId($activityId);

        $this->assertSame($queryBuilder, $event->getQueryBuilder());
        $this->assertSame($user, $event->getUser());
        $this->assertSame($activityId, $event->getActivityId());
    }
} 