<?php

namespace LotteryBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use LotteryBundle\Entity\Prize;
use PHPUnit\Framework\TestCase;
use Tourze\Arrayable\AdminArrayInterface;

class PoolTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
    {
        $pool = new Pool();

        $this->assertEquals(0, $pool->getId());
        $this->assertFalse($pool->isValid());
        $this->assertInstanceOf(Collection::class, $pool->getPrizes());
        $this->assertCount(0, $pool->getPrizes());
        $this->assertInstanceOf(Collection::class, $pool->getActivities());
        $this->assertCount(0, $pool->getActivities());
        $this->assertInstanceOf(Collection::class, $pool->getPoolAttributes());
        $this->assertCount(0, $pool->getPoolAttributes());
    }

    public function test_implements_required_interfaces(): void
    {
        $pool = new Pool();

        $this->assertInstanceOf(\Stringable::class, $pool);
        $this->assertInstanceOf(AdminArrayInterface::class, $pool);
    }

    public function test_setTitle_setsAndReturnsTitle(): void
    {
        $pool = new Pool();
        $title = 'Test Pool';

        $result = $pool->setTitle($title);

        $this->assertEquals($title, $pool->getTitle());
        $this->assertSame($pool, $result);
    }

    public function test_setValid_setsAndReturnsValid(): void
    {
        $pool = new Pool();

        $result = $pool->setValid(true);

        $this->assertTrue($pool->isValid());
        $this->assertSame($pool, $result);
    }

    public function test_setValid_withNull_setsNull(): void
    {
        $pool = new Pool();

        $result = $pool->setValid(null);

        $this->assertNull($pool->isValid());
        $this->assertSame($pool, $result);
    }

    public function test_prizes_addAndRemove(): void
    {
        $pool = new Pool();
        $prize = $this->createMock(Prize::class);

        // 模拟 Prize 的 setPool 方法 
        $prize->expects($this->exactly(2))
            ->method('setPool')
            ->with($this->logicalOr($pool, null));

        $result = $pool->addPrize($prize);

        $this->assertTrue($pool->getPrizes()->contains($prize));
        $this->assertSame($pool, $result);

        // 测试移除
        $prize->expects($this->once())
            ->method('getPool')
            ->willReturn($pool);

        $removeResult = $pool->removePrize($prize);

        $this->assertFalse($pool->getPrizes()->contains($prize));
        $this->assertSame($pool, $removeResult);
    }

    public function test_prizes_addDuplicate_doesNotDuplicate(): void
    {
        $pool = new Pool();
        $prize = $this->createMock(Prize::class);

        $prize->expects($this->once())
            ->method('setPool')
            ->with($pool);

        $pool->addPrize($prize);
        $pool->addPrize($prize); // 添加相同的奖品

        $this->assertCount(1, $pool->getPrizes());
    }

    public function test_activities_addAndRemove(): void
    {
        $pool = new Pool();
        $activity = $this->createMock(Activity::class);

        // 模拟 Activity 的 addPool 方法
        $activity->expects($this->once())
            ->method('addPool')
            ->with($pool);

        $result = $pool->addActivity($activity);

        $this->assertTrue($pool->getActivities()->contains($activity));
        $this->assertSame($pool, $result);

        // 测试移除
        $activity->expects($this->once())
            ->method('removePool')
            ->with($pool);

        $removeResult = $pool->removeActivity($activity);

        $this->assertFalse($pool->getActivities()->contains($activity));
        $this->assertSame($pool, $removeResult);
    }

    public function test_activities_addDuplicate_doesNotDuplicate(): void
    {
        $pool = new Pool();
        $activity = $this->createMock(Activity::class);

        $activity->expects($this->once())
            ->method('addPool')
            ->with($pool);

        $pool->addActivity($activity);
        $pool->addActivity($activity); // 添加相同的活动

        $this->assertCount(1, $pool->getActivities());
    }

    public function test_poolAttributes_addAndRemove(): void
    {
        $pool = new Pool();
        $poolAttribute = $this->createMock(PoolAttribute::class);

        // 模拟 PoolAttribute 的 setPool 方法 
        $poolAttribute->expects($this->exactly(2))
            ->method('setPool')
            ->with($this->logicalOr($pool, null));

        $result = $pool->addPoolAttribute($poolAttribute);

        $this->assertTrue($pool->getPoolAttributes()->contains($poolAttribute));
        $this->assertSame($pool, $result);

        // 测试移除
        $poolAttribute->expects($this->once())
            ->method('getPool')
            ->willReturn($pool);

        $removeResult = $pool->removePoolAttribute($poolAttribute);

        $this->assertFalse($pool->getPoolAttributes()->contains($poolAttribute));
        $this->assertSame($pool, $removeResult);
    }

    public function test_poolAttributes_addDuplicate_doesNotDuplicate(): void
    {
        $pool = new Pool();
        $poolAttribute = $this->createMock(PoolAttribute::class);

        $poolAttribute->expects($this->once())
            ->method('setPool')
            ->with($pool);

        $pool->addPoolAttribute($poolAttribute);
        $pool->addPoolAttribute($poolAttribute); // 添加相同的属性

        $this->assertCount(1, $pool->getPoolAttributes());
    }

    public function test_ipFields_settersAndGetters(): void
    {
        $pool = new Pool();
        $createIp = '192.168.1.1';
        $updateIp = '192.168.1.2';

        $result1 = $pool->setCreatedFromIp($createIp);
        $result2 = $pool->setUpdatedFromIp($updateIp);

        $this->assertEquals($createIp, $pool->getCreatedFromIp());
        $this->assertEquals($updateIp, $pool->getUpdatedFromIp());
        $this->assertSame($pool, $result1);
        $this->assertSame($pool, $result2);
    }

    public function test_toString_withNullOrZeroId_returnsEmptyString(): void
    {
        $pool = new Pool();

        $this->assertEquals('', (string) $pool);
    }

    public function test_toString_withValidIdAndTitle_returnsTitle(): void
    {
        $pool = new Pool();
        $pool->setTitle('Test Pool Title');

        // 通过反射设置ID值
        $reflection = new \ReflectionClass($pool);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($pool, 123);

        $this->assertEquals('Test Pool Title', (string) $pool);
    }
} 