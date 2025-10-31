<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Event\DecidePrizeProbabilityEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(DecidePrizeProbabilityEvent::class)]
final class DecidePrizeProbabilityEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new DecidePrizeProbabilityEvent();

        $this->assertInstanceOf(DecidePrizeProbabilityEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证DecidePrizeProbabilityEvent与Chance的关联关系设置
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }

    public function testSetPrizeSetsAndGetsPrize(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证DecidePrizeProbabilityEvent与Prize的关联关系设置
         * 2) 使用合理性：Prize是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);

        $event->setPrize($prize);

        $this->assertSame($prize, $event->getPrize());
    }

    public function testSetRateWithIntegerSetsAndGetsRate(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $rate = 50;

        $event->setRate($rate);

        $this->assertSame($rate, $event->getRate());
    }

    public function testSetRateWithFloatSetsAndGetsRate(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        $rate = 75.5;

        $event->setRate($rate);

        $this->assertSame($rate, $event->getRate());
    }

    public function testFullWorkflowSetAndGetAllProperties(): void
    {
        $event = new DecidePrizeProbabilityEvent();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证该类的具体行为和功能
         * 3) 替代方案：暂无更好方案，该类没有对应的接口
         */
        $chance = $this->createMock(Chance::class);
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：业务实体类，测试需要验证与实体的关联关系
         * 2) 使用合理性：测试需要验证该类的具体行为和功能
         * 3) 替代方案：暂无更好方案，该类没有对应的接口
         */
        $prize = $this->createMock(Prize::class);
        $rate = 85.7;

        $event->setChance($chance);
        $event->setPrize($prize);
        $event->setRate($rate);

        $this->assertSame($chance, $event->getChance());
        $this->assertSame($prize, $event->getPrize());
        $this->assertSame($rate, $event->getRate());
    }
}
