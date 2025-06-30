<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConsigneeTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
    {
        $consignee = new Consignee();

        $this->assertNull($consignee->getId());
        $this->assertNull($consignee->getRealName());
        $this->assertNull($consignee->getMobile());
        $this->assertNull($consignee->getAddress());
        $this->assertNull($consignee->getChance());
        $this->assertNull($consignee->getCreatedFromIp());
        $this->assertNull($consignee->getUpdatedFromIp());
    }

    public function test_setRealName_setsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testName = '张三';

        $result = $consignee->setRealName($testName);

        $this->assertSame($consignee, $result);
        $this->assertSame($testName, $consignee->getRealName());
    }

    public function test_setMobile_setsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testMobile = '13800138000';

        $result = $consignee->setMobile($testMobile);

        $this->assertSame($consignee, $result);
        $this->assertSame($testMobile, $consignee->getMobile());
    }

    public function test_setAddress_setsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testAddress = '北京市朝阳区某某街道123号';

        $result = $consignee->setAddress($testAddress);

        $this->assertSame($consignee, $result);
        $this->assertSame($testAddress, $consignee->getAddress());
    }

    public function test_setChance_setsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $chance = $this->createMock(Chance::class);

        $result = $consignee->setChance($chance);

        $this->assertSame($consignee, $result);
        $this->assertSame($chance, $consignee->getChance());
    }

    public function test_setCreatedFromIp_setsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testIp = '192.168.1.1';

        $result = $consignee->setCreatedFromIp($testIp);

        $this->assertSame($consignee, $result);
        $this->assertSame($testIp, $consignee->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_setsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testIp = '192.168.1.2';

        $result = $consignee->setUpdatedFromIp($testIp);

        $this->assertSame($consignee, $result);
        $this->assertSame($testIp, $consignee->getUpdatedFromIp());
    }

    public function test_toString_withNullId_returnsEmptyString(): void
    {
        $consignee = new Consignee();

        $this->assertSame('', $consignee->__toString());
    }

    public function test_toString_withZeroId_returnsEmptyString(): void
    {
        $consignee = new Consignee();
        $reflection = new ReflectionClass($consignee);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($consignee, 0);

        $this->assertSame('', $consignee->__toString());
    }

    public function test_toString_withValidId_returnsFormattedString(): void
    {
        $consignee = new Consignee();
        $reflection = new ReflectionClass($consignee);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($consignee, 1);

        $consignee->setRealName('张三')
            ->setMobile('13800138000')
            ->setAddress('北京市朝阳区某某街道123号');

        $expected = '张三 13800138000 北京市朝阳区某某街道123号';
        $this->assertSame($expected, $consignee->__toString());
    }

    public function test_implementsStringable(): void
    {
        $consignee = new Consignee();

        $this->assertInstanceOf(\Stringable::class, $consignee);
    }

    public function test_implementsItemable(): void
    {
        $consignee = new Consignee();

        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $consignee);
    }

    public function test_implementsPlainArrayInterface(): void
    {
        $consignee = new Consignee();

        $this->assertInstanceOf(\Tourze\Arrayable\PlainArrayInterface::class, $consignee);
    }

    public function test_toSelectItem_returnsCorrectArray(): void
    {
        $consignee = new Consignee();
        $reflection = new ReflectionClass($consignee);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($consignee, 1);

        $consignee->setRealName('张三')
            ->setMobile('13800138000')
            ->setAddress('北京市朝阳区某某街道123号');

        $result = $consignee->toSelectItem();
        $expected = '张三 13800138000 北京市朝阳区某某街道123号';

        $this->assertSame($expected, $result['label']);
        $this->assertSame($expected, $result['text']);
        $this->assertSame(1, $result['value']);
        $this->assertSame($expected, $result['name']);
    }

    public function test_retrievePlainArray_returnsCorrectArray(): void
    {
        $consignee = new Consignee();
        $consignee->setRealName('张三')
            ->setMobile('13800138000')
            ->setAddress('北京市朝阳区某某街道123号');

        $result = $consignee->retrievePlainArray();

        $this->assertSame('张三', $result['realName']);
        $this->assertSame('13800138000', $result['mobile']);
        $this->assertSame('北京市朝阳区某某街道123号', $result['address']);
        $this->assertCount(3, $result);
    }

    public function test_setRealName_withEmptyString_setsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setRealName('');

        $this->assertSame('', $consignee->getRealName());
    }

    public function test_setMobile_withEmptyString_setsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setMobile('');

        $this->assertSame('', $consignee->getMobile());
    }

    public function test_setAddress_withEmptyString_setsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setAddress('');

        $this->assertSame('', $consignee->getAddress());
    }

    public function test_setCreatedFromIp_withNull_setsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setCreatedFromIp(null);

        $this->assertNull($consignee->getCreatedFromIp());
    }

    public function test_setUpdatedFromIp_withNull_setsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setUpdatedFromIp(null);

        $this->assertNull($consignee->getUpdatedFromIp());
    }
}
