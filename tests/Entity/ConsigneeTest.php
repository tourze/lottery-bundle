<?php

namespace LotteryBundle\Tests\Entity;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Consignee::class)]
final class ConsigneeTest extends AbstractEntityTestCase
{
    protected function createEntity(): Consignee
    {
        return new Consignee();
    }

    public function testConstructorSetsDefaultValues(): void
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

    public function testSetRealNameSetsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testName = '张三';

        $consignee->setRealName($testName);

        $this->assertSame($testName, $consignee->getRealName());
    }

    public function testSetMobileSetsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testMobile = '13800138000';

        $consignee->setMobile($testMobile);

        $this->assertSame($testMobile, $consignee->getMobile());
    }

    public function testSetAddressSetsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testAddress = '北京市朝阳区某某街道123号';

        $consignee->setAddress($testAddress);

        $this->assertSame($testAddress, $consignee->getAddress());
    }

    public function testSetChanceSetsAndGetsValue(): void
    {
        $consignee = new Consignee();
        /*
         * 使用具体类 Chance 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Consignee与Chance的关联关系设置
         * 2) 使用合理性：Chance是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Chance没有对应的接口
         */
        $chance = $this->createMock(Chance::class);

        $consignee->setChance($chance);
        $this->assertSame($chance, $consignee->getChance());
    }

    public function testSetCreatedFromIpSetsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testIp = '192.168.1.1';

        $consignee->setCreatedFromIp($testIp);

        $this->assertSame($testIp, $consignee->getCreatedFromIp());
    }

    public function testSetUpdatedFromIpSetsAndGetsValue(): void
    {
        $consignee = new Consignee();
        $testIp = '192.168.1.2';

        $consignee->setUpdatedFromIp($testIp);

        $this->assertSame($testIp, $consignee->getUpdatedFromIp());
    }

    public function testToStringWithNullIdReturnsEmptyString(): void
    {
        $consignee = new Consignee();

        $this->assertSame('', $consignee->__toString());
    }

    public function testToStringWithZeroIdReturnsEmptyString(): void
    {
        $consignee = new Consignee();
        $reflection = new \ReflectionClass($consignee);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($consignee, 0);

        $this->assertSame('', $consignee->__toString());
    }

    public function testToStringWithValidIdReturnsFormattedString(): void
    {
        $consignee = new Consignee();
        $reflection = new \ReflectionClass($consignee);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($consignee, 1);

        $consignee->setRealName('张三');
        $consignee->setMobile('13800138000');
        $consignee->setAddress('北京市朝阳区某某街道123号');

        $expected = '张三 13800138000 北京市朝阳区某某街道123号';
        $this->assertSame($expected, $consignee->__toString());
    }

    public function testImplementsStringable(): void
    {
        $consignee = new Consignee();

        $this->assertInstanceOf(\Stringable::class, $consignee);
    }

    public function testImplementsItemable(): void
    {
        $consignee = new Consignee();

        $this->assertInstanceOf(Itemable::class, $consignee);
    }

    public function testImplementsPlainArrayInterface(): void
    {
        $consignee = new Consignee();

        $this->assertInstanceOf(PlainArrayInterface::class, $consignee);
    }

    public function testToSelectItemReturnsCorrectArray(): void
    {
        $consignee = new Consignee();
        $reflection = new \ReflectionClass($consignee);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($consignee, 1);

        $consignee->setRealName('张三');
        $consignee->setMobile('13800138000');
        $consignee->setAddress('北京市朝阳区某某街道123号');

        $result = $consignee->toSelectItem();
        $expected = '张三 13800138000 北京市朝阳区某某街道123号';

        $this->assertSame($expected, $result['label']);
        $this->assertSame($expected, $result['text']);
        $this->assertSame(1, $result['value']);
        $this->assertSame($expected, $result['name']);
    }

    public function testRetrievePlainArrayReturnsCorrectArray(): void
    {
        $consignee = new Consignee();
        $consignee->setRealName('张三');
        $consignee->setMobile('13800138000');
        $consignee->setAddress('北京市朝阳区某某街道123号');

        $result = $consignee->retrievePlainArray();

        $this->assertSame('张三', $result['realName']);
        $this->assertSame('13800138000', $result['mobile']);
        $this->assertSame('北京市朝阳区某某街道123号', $result['address']);
        $this->assertCount(3, $result);
    }

    public function testSetRealNameWithEmptyStringSetsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setRealName('');

        $this->assertSame('', $consignee->getRealName());
    }

    public function testSetMobileWithEmptyStringSetsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setMobile('');

        $this->assertSame('', $consignee->getMobile());
    }

    public function testSetAddressWithEmptyStringSetsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setAddress('');

        $this->assertSame('', $consignee->getAddress());
    }

    public function testSetCreatedFromIpWithNullSetsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setCreatedFromIp(null);

        $this->assertNull($consignee->getCreatedFromIp());
    }

    public function testSetUpdatedFromIpWithNullSetsValue(): void
    {
        $consignee = new Consignee();

        $consignee->setUpdatedFromIp(null);

        $this->assertNull($consignee->getUpdatedFromIp());
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'realName' => ['realName', '张三'];
        yield 'mobile' => ['mobile', '13800138000'];
        yield 'address' => ['address', '北京市朝阳区某某街道123号'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
