<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(PoolAttribute::class)]
final class PoolAttributeTest extends AbstractEntityTestCase
{
    protected function createEntity(): PoolAttribute
    {
        return new PoolAttribute();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $attribute = new PoolAttribute();

        $this->assertEquals(0, $attribute->getId());
        $this->assertNull($attribute->getName());
        $this->assertNull($attribute->getValue());
        $this->assertNull($attribute->getRemark());
        $this->assertNull($attribute->getPool());
    }

    public function testImplementsStringable(): void
    {
        $attribute = new PoolAttribute();

        $this->assertInstanceOf(\Stringable::class, $attribute);
    }

    public function testSetNameSetsAndReturnsName(): void
    {
        $attribute = new PoolAttribute();
        $name = 'test_attribute';

        $attribute->setName($name);

        $this->assertEquals($name, $attribute->getName());
    }

    public function testSetValueSetsAndReturnsValue(): void
    {
        $attribute = new PoolAttribute();
        $value = 'test_value';

        $attribute->setValue($value);

        $this->assertEquals($value, $attribute->getValue());
    }

    public function testSetRemarkSetsAndReturnsRemark(): void
    {
        $attribute = new PoolAttribute();
        $remark = 'test remark';

        $attribute->setRemark($remark);

        $this->assertEquals($remark, $attribute->getRemark());
    }

    public function testSetRemarkWithNullSetsNull(): void
    {
        $attribute = new PoolAttribute();

        $attribute->setRemark(null);

        $this->assertNull($attribute->getRemark());
    }

    public function testSetPoolSetsAndReturnsPool(): void
    {
        $attribute = new PoolAttribute();
        $pool = new Pool();
        $pool->setTitle('Test Pool');

        $attribute->setPool($pool);

        $this->assertSame($pool, $attribute->getPool());
    }

    public function testSetPoolWithNullSetsNull(): void
    {
        $attribute = new PoolAttribute();

        $attribute->setPool(null);

        $this->assertNull($attribute->getPool());
    }

    public function testToStringWithNullOrZeroIdReturnsEmptyString(): void
    {
        $attribute = new PoolAttribute();

        $this->assertEquals('', (string) $attribute);
    }

    public function testToStringWithNameAndValueReturnsFormattedString(): void
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

    public function testIpFieldsSettersAndGetters(): void
    {
        $attribute = new PoolAttribute();
        $createIp = '192.168.1.1';
        $updateIp = '192.168.1.2';

        $attribute->setCreatedFromIp($createIp);
        $attribute->setUpdatedFromIp($updateIp);

        $this->assertEquals($createIp, $attribute->getCreatedFromIp());
        $this->assertEquals($updateIp, $attribute->getUpdatedFromIp());
    }

    public function testIpFieldsWithNullSetsNull(): void
    {
        $attribute = new PoolAttribute();

        $attribute->setCreatedFromIp(null);
        $attribute->setUpdatedFromIp(null);

        $this->assertNull($attribute->getCreatedFromIp());
        $this->assertNull($attribute->getUpdatedFromIp());
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'test_attribute'];
        yield 'value' => ['value', 'test_value'];
        yield 'remark' => ['remark', 'test remark'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
