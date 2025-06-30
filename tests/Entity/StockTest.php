<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use PHPUnit\Framework\TestCase;

class StockTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
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

    public function test_setSn_setsAndGetsValue(): void
    {
        $stock = new Stock();
        $testSn = 'SN123456789';

        $result = $stock->setSn($testSn);

        $this->assertSame($stock, $result);
        $this->assertSame($testSn, $stock->getSn());
    }

    public function test_setPrize_setsAndGetsValue(): void
    {
        $stock = new Stock();
        $prize = $this->createMock(Prize::class);

        $result = $stock->setPrize($prize);

        $this->assertSame($stock, $result);
        $this->assertSame($prize, $stock->getPrize());
    }

    public function test_setChance_setsAndGetsValue(): void
    {
        $stock = new Stock();
        $chance = $this->createMock(Chance::class);

        $result = $stock->setChance($chance);

        $this->assertSame($stock, $result);
        $this->assertSame($chance, $stock->getChance());
    }

    public function test_setLockVersion_setsAndGetsValue(): void
    {
        $stock = new Stock();
        $testLockVersion = 5;

        $result = $stock->setLockVersion($testLockVersion);

        $this->assertSame($stock, $result);
        $this->assertSame($testLockVersion, $stock->getLockVersion());
    }

    public function test_setCreatedFromIp_setsAndGetsValue(): void
    {
        $stock = new Stock();
        $testIp = '192.168.1.1';

        $result = $stock->setCreatedFromIp($testIp);

        $this->assertSame($stock, $result);
        $this->assertSame($testIp, $stock->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_setsAndGetsValue(): void
    {
        $stock = new Stock();
        $testIp = '192.168.1.2';

        $result = $stock->setUpdatedFromIp($testIp);

        $this->assertSame($stock, $result);
        $this->assertSame($testIp, $stock->getUpdatedFromIp());
    }

    public function test_toString_withPrizeAndSn_returnsFormattedString(): void
    {
        $stock = new Stock();
        $prize = $this->createMock(Prize::class);
        $prize->expects($this->once())
            ->method('getName')
            ->willReturn('测试奖品');

        $stock->setPrize($prize)
            ->setSn('SN123456789');

        $expected = '测试奖品 SN123456789';
        $this->assertSame($expected, $stock->__toString());
    }

    public function test_implementsStringable(): void
    {
        $stock = new Stock();

        $this->assertInstanceOf(\Stringable::class, $stock);
    }

    public function test_setPrize_withNull_setsValue(): void
    {
        $stock = new Stock();

        $stock->setPrize(null);

        $this->assertNull($stock->getPrize());
    }

    public function test_setChance_withNull_setsValue(): void
    {
        $stock = new Stock();

        $stock->setChance(null);

        $this->assertNull($stock->getChance());
    }

    public function test_setLockVersion_withNull_setsValue(): void
    {
        $stock = new Stock();

        $stock->setLockVersion(null);

        $this->assertNull($stock->getLockVersion());
    }

    public function test_setCreatedFromIp_withNull_setsValue(): void
    {
        $stock = new Stock();

        $stock->setCreatedFromIp(null);

        $this->assertNull($stock->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_withNull_setsValue(): void
    {
        $stock = new Stock();

        $stock->setUpdatedFromIp(null);

        $this->assertNull($stock->getUpdatedFromIp());
    }

    public function test_setSn_withEmptyString_setsValue(): void
    {
        $stock = new Stock();

        $stock->setSn('');

        $this->assertSame('', $stock->getSn());
    }

    public function test_setLockVersion_withZero_setsValue(): void
    {
        $stock = new Stock();

        $stock->setLockVersion(0);

        $this->assertSame(0, $stock->getLockVersion());
    }

    public function test_fluentInterface_chainedCalls(): void
    {
        $stock = new Stock();
        $prize = $this->createMock(Prize::class);
        $chance = $this->createMock(Chance::class);

        $result = $stock->setSn('SN123')
            ->setPrize($prize)
            ->setChance($chance)
            ->setLockVersion(1)
            ->setCreatedFromIp('192.168.1.1')
            ->setUpdatedFromIp('192.168.1.2');

        $this->assertSame($stock, $result);
        $this->assertSame('SN123', $stock->getSn());
        $this->assertSame($prize, $stock->getPrize());
        $this->assertSame($chance, $stock->getChance());
        $this->assertSame(1, $stock->getLockVersion());
        $this->assertSame('192.168.1.1', $stock->getCreatedFromIp());
        $this->assertSame('192.168.1.2', $stock->getUpdatedFromIp());
    }
} 