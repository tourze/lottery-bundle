<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Stock::class)]
final class StockTest extends AbstractEntityTestCase
{
    protected function createEntity(): Stock
    {
        return new Stock();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $stock = new Stock();

        $this->assertNull($stock->getId());
        $this->assertNull($stock->getSn());
        $this->assertNull($stock->getPrize());
        $this->assertNull($stock->getChance());
        $this->assertNull($stock->getLockVersion());
        $this->assertNull($stock->getCreatedFromIp());
        $this->assertNull($stock->getUpdatedFromIp());
    }

    public function testSetSnSetsAndGetsValue(): void
    {
        $stock = new Stock();
        $testSn = 'SN123456789';

        $stock->setSn($testSn);
        $this->assertSame($testSn, $stock->getSn());
    }

    public function testSetPrizeSetsAndGetsValue(): void
    {
        $stock = new Stock();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Stock与Prize的关联关系设置
         * 2) 使用合理性：Prize是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);

        $stock->setPrize($prize);
        $this->assertSame($prize, $stock->getPrize());
    }

    public function testSetChanceSetsAndGetsValue(): void
    {
        $stock = new Stock();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Stock与Chance的关联关系设置
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $stock->setChance($chance);
        $this->assertSame($chance, $stock->getChance());
    }

    public function testSetLockVersionSetsAndGetsValue(): void
    {
        $stock = new Stock();
        $testLockVersion = 5;

        $stock->setLockVersion($testLockVersion);
        $this->assertSame($testLockVersion, $stock->getLockVersion());
    }

    public function testSetCreatedFromIpSetsAndGetsValue(): void
    {
        $stock = new Stock();
        $testIp = '192.168.1.1';

        $stock->setCreatedFromIp($testIp);
        $this->assertSame($testIp, $stock->getCreatedFromIp());
    }

    public function testSetUpdatedFromIpSetsAndGetsValue(): void
    {
        $stock = new Stock();
        $testIp = '192.168.1.2';

        $stock->setUpdatedFromIp($testIp);
        $this->assertSame($testIp, $stock->getUpdatedFromIp());
    }

    public function testToStringWithPrizeAndSnReturnsFormattedString(): void
    {
        $stock = new Stock();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Stock的__toString方法中使用Prize的名称
         * 2) 使用合理性：Prize是Entity类，测试需要模拟getName方法返回值
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);
        $prize->expects($this->once())
            ->method('getName')
            ->willReturn('测试奖品')
        ;

        $stock->setPrize($prize);
        $stock->setSn('SN123456789');

        $expected = '测试奖品 SN123456789';
        $this->assertSame($expected, $stock->__toString());
    }

    public function testImplementsStringable(): void
    {
        $stock = new Stock();

        $this->assertInstanceOf(\Stringable::class, $stock);
    }

    public function testSetPrizeWithNullSetsValue(): void
    {
        $stock = new Stock();

        $stock->setPrize(null);

        $this->assertNull($stock->getPrize());
    }

    public function testSetChanceWithNullSetsValue(): void
    {
        $stock = new Stock();

        $stock->setChance(null);

        $this->assertNull($stock->getChance());
    }

    public function testSetLockVersionWithNullSetsValue(): void
    {
        $stock = new Stock();

        $stock->setLockVersion(null);

        $this->assertNull($stock->getLockVersion());
    }

    public function testSetCreatedFromIpWithNullSetsValue(): void
    {
        $stock = new Stock();

        $stock->setCreatedFromIp(null);

        $this->assertNull($stock->getCreatedFromIp());
    }

    public function testSetUpdatedFromIpWithNullSetsValue(): void
    {
        $stock = new Stock();

        $stock->setUpdatedFromIp(null);

        $this->assertNull($stock->getUpdatedFromIp());
    }

    public function testSetSnWithEmptyStringSetsValue(): void
    {
        $stock = new Stock();

        $stock->setSn('');

        $this->assertSame('', $stock->getSn());
    }

    public function testSetLockVersionWithZeroSetsValue(): void
    {
        $stock = new Stock();

        $stock->setLockVersion(0);

        $this->assertSame(0, $stock->getLockVersion());
    }

    public function testFluentInterfaceChainedCalls(): void
    {
        $stock = new Stock();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Stock的流式接口设置关联对象
         * 2) 使用合理性：Prize是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Stock的流式接口设置关联对象
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $stock->setSn('SN123');
        $stock->setPrize($prize);
        $stock->setChance($chance);
        $stock->setLockVersion(1);
        $stock->setCreatedFromIp('192.168.1.1');
        $stock->setUpdatedFromIp('192.168.1.2');
        $this->assertSame('SN123', $stock->getSn());
        $this->assertSame($prize, $stock->getPrize());
        $this->assertSame($chance, $stock->getChance());
        $this->assertSame(1, $stock->getLockVersion());
        $this->assertSame('192.168.1.1', $stock->getCreatedFromIp());
        $this->assertSame('192.168.1.2', $stock->getUpdatedFromIp());
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'sn' => ['sn', 'SN123456789'];
        yield 'lockVersion' => ['lockVersion', 5];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
