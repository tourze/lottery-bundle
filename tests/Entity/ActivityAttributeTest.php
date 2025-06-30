<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\ActivityAttribute;
use PHPUnit\Framework\TestCase;

class ActivityAttributeTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
    {
        $attribute = new ActivityAttribute();

        $this->assertNull($attribute->getId());
        $this->assertNull($attribute->getName());
        $this->assertNull($attribute->getValue());
        $this->assertNull($attribute->getRemark());
        $this->assertNull($attribute->getActivity());
    }

    public function test_setName_setsAndReturnsName(): void
    {
        $attribute = new ActivityAttribute();
        $name = 'test_attribute';

        $result = $attribute->setName($name);

        $this->assertEquals($name, $attribute->getName());
        $this->assertSame($attribute, $result);
    }

    public function test_setValue_setsAndReturnsValue(): void
    {
        $attribute = new ActivityAttribute();
        $value = 'test_value';

        $result = $attribute->setValue($value);

        $this->assertEquals($value, $attribute->getValue());
        $this->assertSame($attribute, $result);
    }

    public function test_setRemark_setsAndReturnsRemark(): void
    {
        $attribute = new ActivityAttribute();
        $remark = 'test remark';

        $result = $attribute->setRemark($remark);

        $this->assertEquals($remark, $attribute->getRemark());
        $this->assertSame($attribute, $result);
    }

    public function test_setRemark_withNull_setsNull(): void
    {
        $attribute = new ActivityAttribute();

        $result = $attribute->setRemark(null);

        $this->assertNull($attribute->getRemark());
        $this->assertSame($attribute, $result);
    }

    public function test_setActivity_setsAndReturnsActivity(): void
    {
        $attribute = new ActivityAttribute();
        $activity = $this->createMock(Activity::class);

        $result = $attribute->setActivity($activity);

        $this->assertSame($activity, $attribute->getActivity());
        $this->assertSame($attribute, $result);
    }

    public function test_setActivity_withNull_setsNull(): void
    {
        $attribute = new ActivityAttribute();

        $result = $attribute->setActivity(null);

        $this->assertNull($attribute->getActivity());
        $this->assertSame($attribute, $result);
    }

    public function test_toString_withNullId_returnsEmptyString(): void
    {
        $attribute = new ActivityAttribute();

        $this->assertEquals('', (string) $attribute);
    }

    public function test_toString_withNameAndValue_returnsFormattedString(): void
    {
        $attribute = new ActivityAttribute();
        $attribute->setName('test_name');
        $attribute->setValue('test_value');

        // 为了测试toString，我们需要模拟ID不为null的情况
        // 通过反射设置ID值
        $reflection = new \ReflectionClass($attribute);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($attribute, '12345');

        $this->assertEquals('test_name:test_value', (string) $attribute);
    }

    public function test_ipFields_settersAndGetters(): void
    {
        $attribute = new ActivityAttribute();
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
        $attribute = new ActivityAttribute();

        $result1 = $attribute->setCreatedFromIp(null);
        $result2 = $attribute->setUpdatedFromIp(null);

        $this->assertNull($attribute->getCreatedFromIp());
        $this->assertNull($attribute->getUpdatedFromIp());
        $this->assertSame($attribute, $result1);
        $this->assertSame($attribute, $result2);
    }

    public function test_implements_stringable(): void
    {
        $attribute = new ActivityAttribute();

        $this->assertInstanceOf(\Stringable::class, $attribute);
    }
}
