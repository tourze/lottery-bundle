<?php

namespace LotteryBundle\Tests\Event;

use Doctrine\ORM\QueryBuilder;
use LotteryBundle\Event\AllLotteryChanceEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(AllLotteryChanceEvent::class)]
final class AllLotteryChanceEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new AllLotteryChanceEvent();

        $this->assertInstanceOf(AllLotteryChanceEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetQueryBuilderSetsAndGetsQueryBuilder(): void
    {
        $event = new AllLotteryChanceEvent();

        // 使用 createMock 创建 QueryBuilder，因为它需要 EntityManager
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $event->setQueryBuilder($queryBuilder);

        $this->assertSame($queryBuilder, $event->getQueryBuilder());
    }

    public function testSetUserSetsAndGetsUser(): void
    {
        $event = new AllLotteryChanceEvent();
        $user = new InMemoryUser('test_user', 'password');

        $event->setUser($user);

        $this->assertSame($user, $event->getUser());
    }

    public function testSetActivityIdSetsAndGetsActivityId(): void
    {
        $event = new AllLotteryChanceEvent();
        $activityId = 'activity123';

        $event->setActivityId($activityId);

        $this->assertSame($activityId, $event->getActivityId());
    }

    public function testFullWorkflowSetAndGetAllProperties(): void
    {
        $event = new AllLotteryChanceEvent();

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $user = new InMemoryUser('test_user', 'password');
        $activityId = 'activity456';

        $event->setQueryBuilder($queryBuilder);
        $event->setUser($user);
        $event->setActivityId($activityId);

        $this->assertSame($queryBuilder, $event->getQueryBuilder());
        $this->assertSame($user, $event->getUser());
        $this->assertSame($activityId, $event->getActivityId());
    }
}
