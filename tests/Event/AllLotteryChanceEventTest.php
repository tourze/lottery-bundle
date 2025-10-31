<?php

namespace LotteryBundle\Tests\Event;

use Doctrine\ORM\QueryBuilder;
use LotteryBundle\Event\AllLotteryChanceEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
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

        // 使用具体类进行 mock，因为：
        // 1. QueryBuilder 是 Doctrine ORM 的核心查询构建器，测试中需要验证其具体方法调用
        // 2. 测试需要模拟查询构建逻辑，这是 Event 测试的标准做法
        // 3. 这些类在测试中的使用是为了验证 Event 的数据传递逻辑
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $event->setQueryBuilder($queryBuilder);

        $this->assertSame($queryBuilder, $event->getQueryBuilder());
    }

    public function testSetUserSetsAndGetsUser(): void
    {
        $event = new AllLotteryChanceEvent();
        $user = $this->createMock(UserInterface::class);

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
        // 使用具体类进行 mock，因为：
        // 1. QueryBuilder 是 Doctrine ORM 的核心查询构建器，测试中需要验证其具体方法调用
        // 2. 测试需要模拟查询构建逻辑，这是 Event 测试的标准做法
        // 3. 这些类在测试中的使用是为了验证 Event 的数据传递逻辑
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
