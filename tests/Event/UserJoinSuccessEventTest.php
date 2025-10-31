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
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证UserJoinSuccessEvent与Chance的关联关系设置
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function testSetActivitySetsAndGetsActivity(): void
    {
        $event = new UserJoinSuccessEvent();
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证UserJoinSuccessEvent与Activity的关联关系设置
         * 2) 使用合理性：Activity是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Activity没有对应的接口
         */
        $activity = $this->createMock(Activity::class);

        $event->setActivity($activity);

        $this->assertSame($activity, $event->getActivity());
    }

    public function testGetContextReturnsArrayWithChanceAndActivity(): void
    {
        $event = new UserJoinSuccessEvent();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证该类的具体行为和功能
         * 3) 替代方案：暂无更好方案，该类没有对应的接口
         */
        $chance = $this->createMock(Chance::class);
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证该类的具体行为和功能
         * 3) 替代方案：暂无更好方案，该类没有对应的接口
         */
        $activity = $this->createMock(Activity::class);

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
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证该类的具体行为和功能
         * 3) 替代方案：暂无更好方案，该类没有对应的接口
         */
        $chance = $this->createMock(Chance::class);
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证该类的具体行为和功能
         * 3) 替代方案：暂无更好方案，该类没有对应的接口
         */
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
