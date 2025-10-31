<?php

namespace LotteryBundle\Tests\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\ChanceEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\EventDispatcher\Event;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(ChanceEvent::class)]
final class ChanceEventTest extends AbstractEventTestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $event = new ChanceEvent();

        $this->assertInstanceOf(ChanceEvent::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetChanceSetsAndGetsChance(): void
    {
        $event = new ChanceEvent();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证ChanceEvent与Chance的关联关系设置
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $event->setChance($chance);

        $this->assertSame($chance, $event->getChance());
    }
}
