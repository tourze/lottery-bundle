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
        $prize = new Prize();
        $prize->setName('Test Prize');

        $pool->addPrize($prize);

        $this->assertTrue($pool->getPrizes()->contains($prize));
        $this->assertSame($pool, $prize->getPool());

        $pool->removePrize($prize);

        $this->assertFalse($pool->getPrizes()->contains($prize));
        $this->assertNull($prize->getPool());
    }

    public function testPrizesAddDuplicateDoesNotDuplicate(): void
    {
        $pool = new Pool();
        $prize = new Prize();
        $prize->setName('Test Prize');

        $pool->addPrize($prize);
        $pool->addPrize($prize); // 添加相同的奖品

        $this->assertCount(1, $pool->getPrizes());
    }

    public function testActivitiesAddAndRemove(): void
    {
        $pool = new Pool();
        $activity = new Activity();
        $activity->setTitle('Test Activity');

        $pool->addActivity($activity);

        $this->assertTrue($pool->getActivities()->contains($activity));
        $this->assertTrue($activity->getPools()->contains($pool));

        $pool->removeActivity($activity);

        $this->assertFalse($pool->getActivities()->contains($activity));
        $this->assertFalse($activity->getPools()->contains($pool));
    }

    public function testActivitiesAddDuplicateDoesNotDuplicate(): void
    {
        $pool = new Pool();
        $activity = new Activity();
        $activity->setTitle('Test Activity');

        $pool->addActivity($activity);
        $pool->addActivity($activity); // 添加相同的活动

        $this->assertCount(1, $pool->getActivities());
    }

    public function testPoolAttributesAddAndRemove(): void
    {
        $pool = new Pool();
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('Test Attribute');

        $pool->addPoolAttribute($poolAttribute);

        $this->assertTrue($pool->getPoolAttributes()->contains($poolAttribute));
        $this->assertSame($pool, $poolAttribute->getPool());

        $pool->removePoolAttribute($poolAttribute);

        $this->assertFalse($pool->getPoolAttributes()->contains($poolAttribute));
        $this->assertNull($poolAttribute->getPool());
    }

    public function testPoolAttributesAddDuplicateDoesNotDuplicate(): void
    {
        $pool = new Pool();
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('Test Attribute');

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
