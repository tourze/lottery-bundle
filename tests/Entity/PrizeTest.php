<?php

namespace LotteryBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PrizeTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
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

    public function test_setName_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testName = '测试奖品';

        $result = $prize->setName($testName);

        $this->assertSame($prize, $result);
        $this->assertSame($testName, $prize->getName());
    }

    public function test_setContent_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testContent = '这是一个测试奖品的描述';

        $result = $prize->setContent($testContent);

        $this->assertSame($prize, $result);
        $this->assertSame($testContent, $prize->getContent());
    }

    public function test_setType_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testType = 'physical';

        $result = $prize->setType($testType);

        $this->assertSame($prize, $result);
        $this->assertSame($testType, $prize->getType());
    }

    public function test_setTypeId_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testTypeId = 'TYPE123';

        $result = $prize->setTypeId($testTypeId);

        $this->assertSame($prize, $result);
        $this->assertSame($testTypeId, $prize->getTypeId());
    }

    public function test_setAmount_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testAmount = 5;

        $result = $prize->setAmount($testAmount);

        $this->assertSame($prize, $result);
        $this->assertSame($testAmount, $prize->getAmount());
    }

    public function test_setQuantity_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testQuantity = 100;

        $result = $prize->setQuantity($testQuantity);

        $this->assertSame($prize, $result);
        $this->assertSame($testQuantity, $prize->getQuantity());
    }

    public function test_setDayLimit_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testDayLimit = 10;

        $result = $prize->setDayLimit($testDayLimit);

        $this->assertSame($prize, $result);
        $this->assertSame($testDayLimit, $prize->getDayLimit());
    }

    public function test_setProbability_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testProbability = 50;

        $result = $prize->setProbability($testProbability);

        $this->assertSame($prize, $result);
        $this->assertSame($testProbability, $prize->getProbability());
    }

    public function test_setProbabilityExpression_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testExpression = 'random() > 0.5';

        $result = $prize->setProbabilityExpression($testExpression);

        $this->assertSame($prize, $result);
        $this->assertSame($testExpression, $prize->getProbabilityExpression());
    }

    public function test_setValue_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testValue = '99.99';

        $result = $prize->setValue($testValue);

        $this->assertSame($prize, $result);
        $this->assertSame($testValue, $prize->getValue());
    }

    public function test_setExpireDay_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testExpireDay = 30.5;

        $result = $prize->setExpireDay($testExpireDay);

        $this->assertSame($prize, $result);
        $this->assertSame($testExpireDay, $prize->getExpireDay());
    }

    public function test_setExpireTime_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testExpireTime = new \DateTimeImmutable('2025-12-31 23:59:59');

        $result = $prize->setExpireTime($testExpireTime);

        $this->assertSame($prize, $result);
        $this->assertSame($testExpireTime, $prize->getExpireTime());
    }

    public function test_setPicture_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/picture.jpg';

        $result = $prize->setPicture($testPicture);

        $this->assertSame($prize, $result);
        $this->assertSame($testPicture, $prize->getPicture());
    }

    public function test_setSecondPicture_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/second.jpg';

        $result = $prize->setSecondPicture($testPicture);

        $this->assertSame($prize, $result);
        $this->assertSame($testPicture, $prize->getSecondPicture());
    }

    public function test_setPickPicture_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/pick.jpg';

        $prize->setPickPicture($testPicture);

        $this->assertSame($testPicture, $prize->getPickPicture());
    }

    public function test_setConsigneePicture_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testPicture = '/path/to/consignee.jpg';

        $prize->setConsigneePicture($testPicture);

        $this->assertSame($testPicture, $prize->getConsigneePicture());
    }

    public function test_setCanShow_setsAndGetsValue(): void
    {
        $prize = new Prize();

        $result = $prize->setCanShow(false);

        $this->assertSame($prize, $result);
        $this->assertFalse($prize->isCanShow());
    }

    public function test_setCanShowPrize_setsAndGetsValue(): void
    {
        $prize = new Prize();

        $prize->setCanShowPrize(false);

        $this->assertFalse($prize->getCanShowPrize());
    }

    public function test_setIsDefault_setsAndGetsValue(): void
    {
        $prize = new Prize();

        $result = $prize->setIsDefault(true);

        $this->assertSame($prize, $result);
        $this->assertTrue($prize->getIsDefault());
    }

    public function test_setNeedConsignee_setsAndGetsValue(): void
    {
        $prize = new Prize();

        $result = $prize->setNeedConsignee(true);

        $this->assertSame($prize, $result);
        $this->assertTrue($prize->getNeedConsignee());
    }

    public function test_setNeedReview_setsAndGetsValue(): void
    {
        $prize = new Prize();

        $result = $prize->setNeedReview(true);

        $this->assertSame($prize, $result);
        $this->assertTrue($prize->isNeedReview());
    }

    public function test_setPool_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $pool = $this->createMock(Pool::class);

        $result = $prize->setPool($pool);

        $this->assertSame($prize, $result);
        $this->assertSame($pool, $prize->getPool());
    }

    public function test_addStock_addsStockToCollection(): void
    {
        $prize = new Prize();
        $stock = $this->createMock(Stock::class);
        $stock->expects($this->once())
            ->method('setPrize')
            ->with($prize);

        $result = $prize->addStock($stock);

        $this->assertSame($prize, $result);
        $this->assertTrue($prize->getStocks()->contains($stock));
    }

    public function test_addStock_doesNotAddDuplicateStock(): void
    {
        $prize = new Prize();
        $stock = $this->createMock(Stock::class);
        $stock->expects($this->once())
            ->method('setPrize')
            ->with($prize);

        $prize->addStock($stock);
        $prize->addStock($stock); // 尝试添加重复的stock

        $this->assertCount(1, $prize->getStocks());
    }

    public function test_removeStock_removesStockFromCollection(): void
    {
        $prize = new Prize();
        $stock = $this->createMock(Stock::class);
        
        // 先添加stock
        $stock->expects($this->exactly(2))
            ->method('setPrize')
            ->with($this->logicalOr($prize, null));
        $prize->addStock($stock);

        // 设置removeStock时的期望
        $stock->expects($this->once())
            ->method('getPrize')
            ->willReturn($prize);

        $result = $prize->removeStock($stock);

        $this->assertSame($prize, $result);
        $this->assertFalse($prize->getStocks()->contains($stock));
    }

    public function test_setSortNumber_setsAndGetsValue(): void
    {
        $prize = new Prize();
        $testSortNumber = 100;

        $result = $prize->setSortNumber($testSortNumber);

        $this->assertSame($prize, $result);
        $this->assertSame($testSortNumber, $prize->getSortNumber());
    }

    public function test_setValid_setsAndGetsValue(): void
    {
        $prize = new Prize();

        $result = $prize->setValid(true);

        $this->assertSame($prize, $result);
        $this->assertTrue($prize->isValid());
    }

    public function test_toString_withNullId_returnsEmptyString(): void
    {
        $prize = new Prize();

        $this->assertSame('', $prize->__toString());
    }

    public function test_toString_withZeroId_returnsEmptyString(): void
    {
        $prize = new Prize();
        $reflection = new ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 0);

        $this->assertSame('', $prize->__toString());
    }

    public function test_toString_withValidIdAndPool_returnsFormattedString(): void
    {
        $prize = new Prize();
        $reflection = new ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())
            ->method('getTitle')
            ->willReturn('测试奖池');

        $prize->setName('测试奖品')
            ->setPool($pool);

        $expected = '测试奖池-测试奖品';
        $this->assertSame($expected, $prize->__toString());
    }

    public function test_implementsStringable(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(\Stringable::class, $prize);
    }

    public function test_implementsItemable(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $prize);
    }

    public function test_implementsPlainArrayInterface(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(\Tourze\Arrayable\PlainArrayInterface::class, $prize);
    }

    public function test_implementsAdminArrayInterface(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(\Tourze\Arrayable\AdminArrayInterface::class, $prize);
    }

    public function test_implementsLockEntity(): void
    {
        $prize = new Prize();

        $this->assertInstanceOf(\Tourze\LockServiceBundle\Model\LockEntity::class, $prize);
    }

    public function test_toSelectItem_returnsCorrectArray(): void
    {
        $prize = new Prize();
        $reflection = new ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        $prize->setName('测试奖品')
            ->setValid(true);

        $result = $prize->toSelectItem();

        $this->assertSame(1, $result['value']);
        $this->assertSame('测试奖品', $result['label']);
        $this->assertTrue($result['valid']);
    }

    public function test_retrievePlainArray_returnsCorrectArray(): void
    {
        $prize = new Prize();
        $reflection = new ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        $createTime = new \DateTimeImmutable('2025-01-01 12:00:00');
        $updateTime = new \DateTimeImmutable('2025-01-02 12:00:00');
        
        $prize->setName('测试奖品')
            ->setType('physical')
            ->setTypeId('TYPE123')
            ->setPicture('/path/to/picture.jpg')
            ->setSecondPicture('/path/to/second.jpg')
            ->setProbability(50);
        $prize->setPickPicture('/path/to/pick.jpg');
        $prize->setCanShow(true);
        $prize->setCanShowPrize(true);
        $prize->setIsDefault(false)
            ->setValid(true)
            ->setNeedConsignee(false)
            ->setNeedReview(false)
            ->setDayLimit(10)
            ->setCreateTime($createTime)
            ->setUpdateTime($updateTime);

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

    public function test_retrieveAdminArray_returnsCorrectArray(): void
    {
        $prize = new Prize();
        $reflection = new ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 1);

        $pool = $this->createMock(Pool::class);
        $pool->expects($this->once())
            ->method('getTitle')
            ->willReturn('测试奖池');

        $createTime = new \DateTimeImmutable('2025-01-01 12:00:00');
        
        $prize->setName('测试奖品')
            ->setContent('奖品描述')
            ->setType('physical')
            ->setTypeId('TYPE123')
            ->setPool($pool)
            ->setPicture('/path/to/picture.jpg')
            ->setProbability(50)
            ->setValid(true)
            ->setCreateTime($createTime);

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

    public function test_retrieveLockResource_returnsIdAsString(): void
    {
        $prize = new Prize();
        $reflection = new ReflectionClass($prize);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($prize, 123);

        $result = $prize->retrieveLockResource();

        $this->assertSame('123', $result);
    }

    public function test_setContent_withNull_setsValue(): void
    {
        $prize = new Prize();

        $prize->setContent(null);

        $this->assertNull($prize->getContent());
    }

    public function test_setTypeId_withNull_setsValue(): void
    {
        $prize = new Prize();

        $prize->setTypeId(null);

        $this->assertNull($prize->getTypeId());
    }

    public function test_setAmount_withNull_setsValue(): void
    {
        $prize = new Prize();

        $prize->setAmount(null);

        $this->assertNull($prize->getAmount());
    }

    public function test_setExpireTime_withNull_setsValue(): void
    {
        $prize = new Prize();

        $prize->setExpireTime(null);

        $this->assertNull($prize->getExpireTime());
    }

    public function test_setPool_withNull_setsValue(): void
    {
        $prize = new Prize();

        $prize->setPool(null);

        $this->assertNull($prize->getPool());
    }
} 