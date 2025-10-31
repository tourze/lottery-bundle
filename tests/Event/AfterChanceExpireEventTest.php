<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\AfterChanceExpireEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(AfterChanceExpireEvent::class)]
final class AfterChanceExpireEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new AfterChanceExpireEvent();

        $this->assertInstanceOf(AfterChanceExpireEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new AfterChanceExpireEvent();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证AfterChanceExpireEvent与Chance的关联关系设置
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }
}
