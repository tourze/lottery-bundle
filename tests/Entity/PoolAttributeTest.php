<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use PHPUnit\Framework\TestCase;

class PoolAttributeTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
    {
        $attribute = new PoolAttribute();

        $this->assertEquals(0, $attribute->getId());
        $this->assertNull($attribute->getName());
        $this->assertNull($attribute->getValue());
        $this->assertNull($attribute->getRemark());
        $this->assertNull($attribute->getPool());
    }

    public function test_implements_stringable(): void
    {
        $attribute = new PoolAttribute();

        $this->assertInstanceOf(\Stringable::class, $attribute);
    }

    public function test_setName_setsAndReturnsName(): void
    {
        $attribute = new PoolAttribute();
        $name = 'test_attribute';

        $result = $attribute->setName($name);

        $this->assertEquals($name, $attribute->getName());
        $this->assertSame($attribute, $result);
    }

    public function test_setValue_setsAndReturnsValue(): void
    {
        $attribute = new PoolAttribute();
        $value = 'test_value';

        $result = $attribute->setValue($value);

        $this->assertEquals($value, $attribute->getValue());
        $this->assertSame($attribute, $result);
    }

    public function test_setRemark_setsAndReturnsRemark(): void
    {
        $attribute = new PoolAttribute();
        $remark = 'test remark';

        $result = $attribute->setRemark($remark);

        $this->assertEquals($remark, $attribute->getRemark());
        $this->assertSame($attribute, $result);
    }

    public function test_setRemark_withNull_setsNull(): void
    {
        $attribute = new PoolAttribute();

        $result = $attribute->setRemark(null);

        $this->assertNull($attribute->getRemark());
        $this->assertSame($attribute, $result);
    }

    public function test_setPool_setsAndReturnsPool(): void
    {
        $attribute = new PoolAttribute();
        $pool = $this->createMock(Pool::class);

        $result = $attribute->setPool($pool);

        $this->assertSame($pool, $attribute->getPool());
        $this->assertSame($attribute, $result);
    }

    public function test_setPool_withNull_setsNull(): void
    {
        $attribute = new PoolAttribute();

        $result = $attribute->setPool(null);

        $this->assertNull($attribute->getPool());
        $this->assertSame($attribute, $result);
    }

    public function test_toString_withNullOrZeroId_returnsEmptyString(): void
    {
        $attribute = new PoolAttribute();

        $this->assertEquals('', (string) $attribute);
    }

    public function test_toString_withNameAndValue_returnsFormattedString(): void
    {
        $attribute = new PoolAttribute();
        $attribute->setName('test_name');
        $attribute->setValue('test_value');

        // 通过反射设置ID值
        $reflection = new \ReflectionClass($attribute);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($attribute, 123);

        $this->assertEquals('test_name:test_value', (string) $attribute);
    }

    public function test_ipFields_settersAndGetters(): void
    {
        $attribute = new PoolAttribute();
        $createIp = '192.168.1.1';
        $updateIp = '192.168.1.2';

        $result1 = $attribute->setCreatedFromIp($createIp);
        $result2 = $attribute->setUpdatedFromIp($updateIp);

        $this->assertEquals($createIp, $attribute->getCreatedFromIp());
        $this->assertEquals($updateIp, $attribute->getUpdatedFromIp());
        $this->assertSame($attribute, $result1);
        $this->assertSame($attribute, $result2);
    }

    public function test_ipFields_withNull_setsNull(): void
    {
        $attribute = new PoolAttribute();

        $result1 = $attribute->setCreatedFromIp(null);
        $result2 = $attribute->setUpdatedFromIp(null);

        $this->assertNull($attribute->getCreatedFromIp());
        $this->assertNull($attribute->getUpdatedFromIp());
        $this->assertSame($attribute, $result1);
        $this->assertSame($attribute, $result2);
    }
} 