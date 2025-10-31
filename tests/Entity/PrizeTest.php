<?php

namespace LotteryBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Prize::class)]
final class PrizeTest extends AbstractEntityTestCase
{
    protected function createEntity(): Prize
    {
        return new Prize();
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $prize = new Prize();

        $this->assertNull($prize->getId());
        $this->assertNull($prize->getName());
        $this->assertNull($prize->getContent());
        $this->assertNull($prize->getTypeId());
        $this->assertSame(1, $prize->getAmount());
        $this->assertSame(0, $prize->getQuantity());
        $this->assertSame(0, $prize->getDayLimit());
        $this->assertSame(0, $prize->getProbability());
        $this->assertNull($prize->getProbabilityExpression());
        $this->assertNull($prize->getValue());
        $this->assertNull($prize->getExpireDay());
        $this->assertNull($prize->getExpireTime());
        $this->assertNull($prize->getPicture());
        $this->assertNull($prize->getSecondPicture());
        $this->assertNull($prize->getPickPicture());
        $this->assertNull($prize->getConsigneePicture());
        $this->assertTrue($prize->isCanShow());
        $this->assertTrue($prize->getCanShowPrize());
        $this->assertFalse($prize->getIsDefault());
        $this->assertFalse($prize->getNeedConsignee());
        $this->assertFalse($prize->isNeedReview());
        $this->assertNull($prize->getPool());
        $this->assertInstanceOf(ArrayCollection::class, $prize->getStocks());
        $this->assertTrue($prize->getStocks()->isEmpty());
        $this->assertSame(0, $prize->getSortNumber());
        $this->assertFalse($prize->isValid());
    }

    public function testSetNameSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testName = '测试奖品';

        $prize->setName($testName);
        $this->assertSame($testName, $prize->getName());
    }

    public function testSetContentSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testContent = '这是一个测试奖品的描述';

        $prize->setContent($testContent);
        $this->assertSame($testContent, $prize->getContent());
    }

    public function testSetTypeSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testType = 'physical';

        $prize->setType($testType);
        $this->assertSame($testType, $prize->getType());
    }

    public function testSetTypeIdSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testTypeId = 'TYPE123';

        $prize->setTypeId($testTypeId);
        $this->assertSame($testTypeId, $prize->getTypeId());
    }

    public function testSetAmountSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testAmount = 5;

        $prize->setAmount($testAmount);
        $this->assertSame($testAmount, $prize->getAmount());
    }

    public function testSetQuantitySetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testQuantity = 100;

        $prize->setQuantity($testQuantity);
        $this->assertSame($testQuantity, $prize->getQuantity());
    }

    public function testSetDayLimitSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testDayLimit = 10;

        $prize->setDayLimit($testDayLimit);
        $this->assertSame($testDayLimit, $prize->getDayLimit());
    }

    public function testSetProbabilitySetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testProbability = 50;

        $prize->setProbability($testProbability);
        $this->assertSame($testProbability, $prize->getProbability());
    }

    public function testSetProbabilityExpressionSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testExpression = 'random() > 0.5';

        $prize->setProbabilityExpression($testExpression);
        $this->assertSame($testExpression, $prize->getProbabilityExpression());
    }

    public function testSetValueSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testValue = '99.99';

        $prize->setValue($testValue);
        $this->assertSame($testValue, $prize->getValue());
    }

    public function testSetExpireDaySetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testExpireDay = 30.5;

        $prize->setExpireDay($testExpireDay);
        $this->assertSame($testExpireDay, $prize->getExpireDay());
    }

    public function testSetExpireTimeSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testExpireTime = new \DateTimeImmutable('2025-12-31 23:59:59');

        $prize->setExpireTime($testExpireTime);
        $this->assertSame($testExpireTime, $prize->getExpireTime());
    }

    public function testSetPictureSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/picture.jpg';

        $prize->setPicture($testPicture);
        $this->assertSame($testPicture, $prize->getPicture());
    }

    public function testSetSecondPictureSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/second.jpg';

        $prize->setSecondPicture($testPicture);
        $this->assertSame($testPicture, $prize->getSecondPicture());
    }

    public function testSetPickPictureSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/pick.jpg';

        $prize->setPickPicture($testPicture);

        $this->assertSame($testPicture, $prize->getPickPicture());
    }

    public function testSetConsigneePictureSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/consignee.jpg';

        $prize->setConsigneePicture($testPicture);

        $this->assertSame($testPicture, $prize->getConsigneePicture());
    }

    public function testSetCanShowSetsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setCanShow(false);
        $this->assertFalse($prize->isCanShow());
    }

    public function testSetCanShowPrizeSetsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setCanShowPrize(false);

        $this->assertFalse($prize->getCanShowPrize());
    }

    public function testSetIsDefaultSetsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setIsDefault(true);
        $this->assertTrue($prize->getIsDefault());
    }

    public function testSetNeedConsigneeSetsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setNeedConsignee(true);
        $this->assertTrue($prize->getNeedConsignee());
    }

    public function testSetNeedReviewSetsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setNeedReview(true);
        $this->assertTrue($prize->isNeedReview());
    }

    public function testSetPoolSetsAndGetsValue(): void
    {
        $prize = new Prize();
        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Prize与Pool的关联关系设置
         * 2) 使用合理性：Pool是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Pool没有对应的接口
         */
        $pool = $this->createMock(Pool::class);

        $prize->setPool($pool);
        $this->assertSame($pool, $prize->getPool());
    }

    public function testAddStockAddsStockToCollection(): void
    {
        $prize = new Prize();
        /*
         * 使用具体类 Stock 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Prize与Stock的集合关联关系
         * 2) 使用合理性：Stock是Entity类，测试需要模拟setPrize方法
         * 3) 替代方案：暂无更好方案，Stock没有对应的接口
         */
        $stock = $this->createMock(Stock::class);
        $stock->expects($this->once())
            ->method('setPrize')
            ->with($prize)
        ;

        $prize->addStock($stock);
        $this->assertTrue($prize->getStocks()->contains($stock));
    }

    public function testAddStockDoesNotAddDuplicateStock(): void
    {
        $prize = new Prize();
        /*
         * 使用具体类 Stock 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Prize与Stock的集合去重逻辑
         * 2) 使用合理性：Stock是Entity类，测试需要模拟setPrize方法
         * 3) 替代方案：暂无更好方案，Stock没有对应的接口
         */
        $stock = $this->createMock(Stock::class);
        $stock->expects($this->once())
            ->method('setPrize')
            ->with($prize)
        ;

        $prize->addStock($stock);
        $prize->addStock($stock); // 尝试添加重复的stock

        $this->assertCount(1, $prize->getStocks());
    }

    public function testRemoveStockRemovesStockFromCollection(): void
    {
        $prize = new Prize();
        /*
         * 使用具体类 Stock 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Prize与Stock的集合移除逻辑
         * 2) 使用合理性：Stock是Entity类，测试需要模拟setPrize/getPrize方法
         * 3) 替代方案：暂无更好方案，Stock没有对应的接口
         */
        $stock = $this->createMock(Stock::class);

        // 先添加stock
        $stock->expects($this->exactly(2))
            ->method('setPrize')
            ->willReturnCallback(function ($argument) use ($prize) {
                $this->assertTrue($argument === $prize || null === $argument);
            })
        ;
        $prize->addStock($stock);

        // 设置removeStock时的期望
        $stock->expects($this->once())
            ->method('getPrize')
            ->willReturn($prize)
        ;

        $prize->removeStock($stock);
        $this->assertFalse($prize->getStocks()->contains($stock));
    }

    public function testSetSortNumberSetsAndGetsValue(): void
    {
        $prize = new Prize();
        $testSortNumber = 100;

        $prize->setSortNumber($testSortNumber);
        $this->assertSame($testSortNumber, $prize->getSortNumber());
    }

    public function testSetValidSetsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setValid(true);
        $this->assertTrue($prize->isValid());
    }

    public function testToStringWithNullIdReturnsEmptyString(): void
    {
        $prize = new Prize();

        $this->assertSame('', $prize->__toString());
    }

    public function testToStringWithZeroIdReturnsEmptyString(): void
    {
        $prize = new Prize();
        $reflection = new \ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 0);

        $this->assertSame('', $prize->__toString());
    }

    public function testToStringWithValidIdAndPoolReturnsFormattedString(): void
    {
        $prize = new Prize();
        $reflection = new \ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Prize的__toString方法中使用Pool的标题
         * 2) 使用合理性：Pool是Entity类，测试需要模拟getTitle方法返回值
         * 3) 替代方案：暂无更好方案，Pool没有对应的接口
         */
        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())
            ->method('getTitle')
            ->willReturn('测试奖池')
        ;

        $prize->setName('测试奖品');
        $prize->setPool($pool);

        $expected = '测试奖池-测试奖品';
        $this->assertSame($expected, $prize->__toString());
    }

    public function testImplementsStringable(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(\Stringable::class, $prize);
    }

    public function testImplementsItemable(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(Itemable::class, $prize);
    }

    public function testImplementsPlainArrayInterface(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(PlainArrayInterface::class, $prize);
    }

    public function testImplementsAdminArrayInterface(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(AdminArrayInterface::class, $prize);
    }

    public function testImplementsLockEntity(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(LockEntity::class, $prize);
    }

    public function testToSelectItemReturnsCorrectArray(): void
    {
        $prize = new Prize();
        $reflection = new \ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        $prize->setName('测试奖品');
        $prize->setValid(true);

        $result = $prize->toSelectItem();

        $this->assertSame(1, $result['value']);
        $this->assertSame('测试奖品', $result['label']);
        $this->assertTrue($result['valid']);
    }

    public function testRetrievePlainArrayReturnsCorrectArray(): void
    {
        $prize = new Prize();
        $reflection = new \ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        $createTime = new \DateTimeImmutable('2025-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2025-01-02 12:00:00');

        $prize->setName('测试奖品');
        $prize->setType('physical');
        $prize->setTypeId('TYPE123');
        $prize->setPicture('/path/to/picture.jpg');
        $prize->setSecondPicture('/path/to/second.jpg');
        $prize->setProbability(50);
        $prize->setPickPicture('/path/to/pick.jpg');
        $prize->setCanShow(true);
        $prize->setCanShowPrize(true);
        $prize->setIsDefault(false);
        $prize->setValid(true);
        $prize->setNeedConsignee(false);
        $prize->setNeedReview(false);
        $prize->setDayLimit(10);
        $prize->setCreateTime($createTime);
        $prize->setUpdateTime($updateTime);

        $result = $prize->retrievePlainArray();

        $this->assertSame(1, $result['id']);
        $this->assertSame('测试奖品', $result['name']);
        $this->assertSame('physical', $result['type']);
        $this->assertSame('TYPE123', $result['typeId']);
        $this->assertSame('/path/to/picture.jpg', $result['picture']);
        $this->assertSame('/path/to/second.jpg', $result['secondPicture']);
        $this->assertSame('/path/to/pick.jpg', $result['pickPicture']);
        $this->assertSame(50, $result['probability']);
        $this->assertTrue($result['canShow']);
        $this->assertTrue($result['canShowPrize']);
        $this->assertFalse($result['isDefault']);
        $this->assertTrue($result['valid']);
        $this->assertFalse($result['needConsignee']);
        $this->assertFalse($result['needReview']);
        $this->assertSame(10, $result['dayLimit']);
        $this->assertSame('2025-01-01 12:00:00', $result['createTime']);
        $this->assertSame('2025-01-02 12:00:00', $result['updateTime']);
    }

    public function testRetrieveAdminArrayReturnsCorrectArray(): void
    {
        $prize = new Prize();
        $reflection = new \ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Prize的Admin数组中包含Pool标题
         * 2) 使用合理性：Pool是Entity类，测试需要模拟getTitle方法返回值
         * 3) 替代方案：暂无更好方案，Pool没有对应的接口
         */
        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())
            ->method('getTitle')
            ->willReturn('测试奖池')
        ;

        $createTime = new \DateTimeImmutable('2025-01-01 12:00:00');

        $prize->setName('测试奖品');
        $prize->setContent('奖品描述');
        $prize->setType('physical');
        $prize->setTypeId('TYPE123');
        $prize->setPool($pool);
        $prize->setPicture('/path/to/picture.jpg');
        $prize->setProbability(50);
        $prize->setValid(true);
        $prize->setCreateTime($createTime);

        $result = $prize->retrieveAdminArray();

        $this->assertSame(1, $result['id']);
        $this->assertSame('测试奖品', $result['name']);
        $this->assertSame('奖品描述', $result['content']);
        $this->assertSame('physical', $result['type']);
        $this->assertSame('TYPE123', $result['typeId']);
        $this->assertSame('测试奖池', $result['pool']);
        $this->assertSame('/path/to/picture.jpg', $result['picture']);
        $this->assertSame(50, $result['probability']);
        $this->assertTrue($result['valid']);
        $this->assertSame('2025-01-01 12:00:00', $result['createTime']);
    }

    public function testRetrieveLockResourceReturnsIdAsString(): void
    {
        $prize = new Prize();
        $reflection = new \ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 123);

        $result = $prize->retrieveLockResource();

        $this->assertSame('123', $result);
    }

    public function testSetContentWithNullSetsValue(): void
    {
        $prize = new Prize();

        $prize->setContent(null);

        $this->assertNull($prize->getContent());
    }

    public function testSetTypeIdWithNullSetsValue(): void
    {
        $prize = new Prize();

        $prize->setTypeId(null);

        $this->assertNull($prize->getTypeId());
    }

    public function testSetAmountWithNullSetsValue(): void
    {
        $prize = new Prize();

        $prize->setAmount(null);

        $this->assertNull($prize->getAmount());
    }

    public function testSetExpireTimeWithNullSetsValue(): void
    {
        $prize = new Prize();

        $prize->setExpireTime(null);

        $this->assertNull($prize->getExpireTime());
    }

    public function testSetPoolWithNullSetsValue(): void
    {
        $prize = new Prize();

        $prize->setPool(null);

        $this->assertNull($prize->getPool());
    }

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试奖品'];
        yield 'content' => ['content', '这是一个测试奖品的描述'];
        yield 'type' => ['type', 'physical'];
        yield 'typeId' => ['typeId', 'TYPE123'];
        yield 'amount' => ['amount', 5];
        yield 'quantity' => ['quantity', 100];
        yield 'dayLimit' => ['dayLimit', 10];
        yield 'probability' => ['probability', 50];
        yield 'probabilityExpression' => ['probabilityExpression', 'random() > 0.5'];
        yield 'value' => ['value', '99.99'];
        yield 'expireDay' => ['expireDay', 30.5];
        yield 'expireTime' => ['expireTime', new \DateTimeImmutable('2025-12-31 23:59:59')];
        yield 'picture' => ['picture', '/path/to/picture.jpg'];
        yield 'secondPicture' => ['secondPicture', '/path/to/second.jpg'];
        yield 'pickPicture' => ['pickPicture', '/path/to/pick.jpg'];
        yield 'consigneePicture' => ['consigneePicture', '/path/to/consignee.jpg'];
        yield 'canShow' => ['canShow', true];
        yield 'canShowPrize' => ['canShowPrize', true];
        yield 'isDefault' => ['isDefault', false];
        yield 'needConsignee' => ['needConsignee', true];
        yield 'needReview' => ['needReview', true];
        yield 'sortNumber' => ['sortNumber', 100];
        yield 'valid' => ['valid', true];
    }
}
