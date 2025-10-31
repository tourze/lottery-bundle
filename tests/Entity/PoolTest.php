<?php

namespace LotteryBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use LotteryBundle\Entity\Prize;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Pool::class)]
final class PoolTest extends AbstractEntityTestCase
{
    protected function createEntity(): Pool
    {
        return new Pool();
    }

    public function testConstructorSetsDefaultValues(): void
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

    public function testImplementsRequiredInterfaces(): void
    {
        $pool = new Pool();

        $this->assertInstanceOf(\Stringable::class, $pool);
        $this->assertInstanceOf(AdminArrayInterface::class, $pool);
    }

    public function testSetTitleSetsAndReturnsTitle(): void
    {
        $pool = new Pool();
        $title = 'Test Pool';

        $pool->setTitle($title);

        $this->assertEquals($title, $pool->getTitle());
    }

    public function testSetValidSetsAndReturnsValid(): void
    {
        $pool = new Pool();

        $pool->setValid(true);

        $this->assertTrue($pool->isValid());
    }

    public function testSetValidWithNullSetsNull(): void
    {
        $pool = new Pool();

        $pool->setValid(null);

        $this->assertNull($pool->isValid());
    }

    public function testPrizesAddAndRemove(): void
    {
        $pool = new Pool();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Pool与Prize的双向关联关系
         * 2) 使用合理性：Prize是Entity类，测试需要模拟setPool/getPool方法
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);

        // 模拟 Prize 的 setPool 方法
        $prize->expects($this->exactly(2))
            ->method('setPool')
            ->willReturnCallback(function ($argument) use ($pool) {
                $this->assertTrue($argument === $pool || null === $argument);
            })
        ;

        $pool->addPrize($prize);

        $this->assertTrue($pool->getPrizes()->contains($prize));

        // 测试移除
        $prize->expects($this->once())
            ->method('getPool')
            ->willReturn($pool)
        ;

        $pool->removePrize($prize);

        $this->assertFalse($pool->getPrizes()->contains($prize));
    }

    public function testPrizesAddDuplicateDoesNotDuplicate(): void
    {
        $pool = new Pool();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Pool与Prize的集合去重逻辑
         * 2) 使用合理性：Prize是Entity类，测试需要模拟setPool方法
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);

        $prize->expects($this->once())
            ->method('setPool')
            ->with($pool)
        ;

        $pool->addPrize($prize);
        $pool->addPrize($prize); // 添加相同的奖品

        $this->assertCount(1, $pool->getPrizes());
    }

    public function testActivitiesAddAndRemove(): void
    {
        $pool = new Pool();
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Pool与Activity的多对多关联关系
         * 2) 使用合理性：Activity是Entity类，测试需要模拟addPool/removePool方法
         * 3) 替代方案：暂无更好方案，Activity没有对应的接口
         */
        $activity = $this->createMock(Activity::class);

        // 模拟 Activity 的 addPool 方法
        $activity->expects($this->once())
            ->method('addPool')
            ->with($pool)
        ;

        $pool->addActivity($activity);

        $this->assertTrue($pool->getActivities()->contains($activity));

        // 测试移除
        $activity->expects($this->once())
            ->method('removePool')
            ->with($pool)
        ;

        $pool->removeActivity($activity);

        $this->assertFalse($pool->getActivities()->contains($activity));
    }

    public function testActivitiesAddDuplicateDoesNotDuplicate(): void
    {
        $pool = new Pool();
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Pool与Activity的集合去重逻辑
         * 2) 使用合理性：Activity是Entity类，测试需要模拟addPool方法
         * 3) 替代方案：暂无更好方案，Activity没有对应的接口
         */
        $activity = $this->createMock(Activity::class);

        $activity->expects($this->once())
            ->method('addPool')
            ->with($pool)
        ;

        $pool->addActivity($activity);
        $pool->addActivity($activity); // 添加相同的活动

        $this->assertCount(1, $pool->getActivities());
    }

    public function testPoolAttributesAddAndRemove(): void
    {
        $pool = new Pool();
        /*
         * 使用具体类 PoolAttribute 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Pool与PoolAttribute的一对多关联
         * 2) 使用合理性：PoolAttribute是Entity类，测试需要模拟setPool/getPool方法
         * 3) 替代方案：暂无更好方案，PoolAttribute没有对应的接口
         */
        $poolAttribute = $this->createMock(PoolAttribute::class);

        // 模拟 PoolAttribute 的 setPool 方法
        $poolAttribute->expects($this->exactly(2))
            ->method('setPool')
            ->willReturnCallback(function ($argument) use ($pool) {
                $this->assertTrue($argument === $pool || null === $argument);
            })
        ;

        $pool->addPoolAttribute($poolAttribute);

        $this->assertTrue($pool->getPoolAttributes()->contains($poolAttribute));

        // 测试移除
        $poolAttribute->expects($this->once())
            ->method('getPool')
            ->willReturn($pool)
        ;

        $pool->removePoolAttribute($poolAttribute);

        $this->assertFalse($pool->getPoolAttributes()->contains($poolAttribute));
    }

    public function testPoolAttributesAddDuplicateDoesNotDuplicate(): void
    {
        $pool = new Pool();
        /*
         * 使用具体类 PoolAttribute 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Pool与PoolAttribute的集合去重逻辑
         * 2) 使用合理性：PoolAttribute是Entity类，测试需要模拟setPool方法
         * 3) 替代方案：暂无更好方案，PoolAttribute没有对应的接口
         */
        $poolAttribute = $this->createMock(PoolAttribute::class);

        $poolAttribute->expects($this->once())
            ->method('setPool')
            ->with($pool)
        ;

        $pool->addPoolAttribute($poolAttribute);
        $pool->addPoolAttribute($poolAttribute); // 添加相同的属性

        $this->assertCount(1, $pool->getPoolAttributes());
    }

    public function testIpFieldsSettersAndGetters(): void
    {
        $pool = new Pool();
        $createIp = '192.168.1.1';
        $updateIp = '192.168.1.2';

        $pool->setCreatedFromIp($createIp);
        $pool->setUpdatedFromIp($updateIp);

        $this->assertEquals($createIp, $pool->getCreatedFromIp());
        $this->assertEquals($updateIp, $pool->getUpdatedFromIp());
    }

    public function testToStringWithNullOrZeroIdReturnsEmptyString(): void
    {
        $pool = new Pool();

        $this->assertEquals('', (string) $pool);
    }

    public function testToStringWithValidIdAndTitleReturnsTitle(): void
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

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', 'Test Pool'];
        yield 'valid' => ['valid', true];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
