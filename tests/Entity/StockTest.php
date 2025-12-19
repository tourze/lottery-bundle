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
        $prize = new Prize();
        $prize->setName('Test Prize');

        $stock->setPrize($prize);
        $this->assertSame($prize, $stock->getPrize());
    }

    public function testSetChanceSetsAndGetsValue(): void
    {
        $stock = new Stock();
        $chance = new Chance();
        $chance->setTitle('Test Chance');

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
        $prize = new Prize();
        $prize->setName('测试奖品');

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
        $prize = new Prize();
        $prize->setName('Test Prize');
        $chance = new Chance();
        $chance->setTitle('Test Chance');

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
