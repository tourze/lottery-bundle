<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Event\DecidePoolEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
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

        // 使用具体实体类进行 mock，因为：
        // 1. Chance 是业务实体，测试需要验证其具体的业务方法调用
        // 2. 实体类通常没有对应的接口，直接 mock 是测试实体行为的标准做法
        // 3. 测试需要验证实体状态变更和业务逻辑的具体实现
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function testSetPoolSetsAndGetsPool(): void
    {
        $event = new DecidePoolEvent();
        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证DecidePoolEvent与Pool的关联关系设置
         * 2) 使用合理性：Pool是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Pool没有对应的接口
         */
        $pool = $this->createMock(Pool::class);

        $event->setPool($pool);

        $this->assertSame($pool, $event->getPool());
    }

    public function testSetUserSetsAndGetsUser(): void
    {
        $event = new DecidePoolEvent();
        $user = $this->createMock(UserInterface::class);

        $event->setUser($user);

        $this->assertSame($user, $event->getUser());
    }

    public function testSetActivitySetsAndGetsActivity(): void
    {
        $event = new DecidePoolEvent();
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证DecidePoolEvent与Activity的关联关系设置
         * 2) 使用合理性：Activity是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Activity没有对应的接口
         */
        $activity = $this->createMock(Activity::class);

        $event->setActivity($activity);

        $this->assertSame($activity, $event->getActivity());
    }

    public function testSetNullValuesSetsAllToNull(): void
    {
        $event = new DecidePoolEvent();

        // 先设置一些非null值
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：Chance是业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证事件对象的属性设置和获取功能
         * 3) 替代方案：暂无更好方案，Chance实体类没有对应的接口
         */
        $event->setChance($this->createMock(Chance::class));
        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：Pool是业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证事件对象的属性设置和获取功能
         * 3) 替代方案：暂无更好方案，Pool实体类没有对应的接口
         */
        $event->setPool($this->createMock(Pool::class));
        $event->setUser($this->createMock(UserInterface::class));
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：Activity是业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证事件对象的属性设置和获取功能
         * 3) 替代方案：暂无更好方案，Activity实体类没有对应的接口
         */
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

    public function testFullWorkflowSetAndGetAllProperties(): void
    {
        $event = new DecidePoolEvent();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：Chance是业务实体类，测试需要验证完整的工作流程
         * 2) 使用合理性：测试需要验证事件对象的完整功能
         * 3) 替代方案：暂无更好方案，Chance实体类没有对应的接口
         */
        $chance = $this->createMock(Chance::class);
        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：Pool是业务实体类，测试需要验证完整的工作流程
         * 2) 使用合理性：测试需要验证事件对象的完整功能
         * 3) 替代方案：暂无更好方案，Pool实体类没有对应的接口
         */
        $pool = $this->createMock(Pool::class);
        $user = $this->createMock(UserInterface::class);
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：Activity是业务实体类，测试需要验证完整的工作流程
         * 2) 使用合理性：测试需要验证事件对象的完整功能
         * 3) 替代方案：暂无更好方案，Activity实体类没有对应的接口
         */
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
