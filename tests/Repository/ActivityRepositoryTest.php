<?php

namespace LotteryBundle\Tests\Repository;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Repository\ActivityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ActivityRepository::class)]
#[RunTestsInSeparateProcesses]
final class ActivityRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 这个方法必须实现，但可以为空
    }

    public function testSaveMethod(): void
    {
        $entity = new Activity();
        $entity->setTitle('Test Activity ' . uniqid());
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setTextRule('Test activity rules');
        $entity->setValid(true);

        $this->getRepository()->save($entity, true);

        $this->assertNotEquals(0, $entity->getId());
        $this->assertStringContainsString('Test Activity', $entity->getTitle());
        $this->assertEquals('Test activity rules', $entity->getTextRule());
        $this->assertTrue($entity->isValid());
    }

    public function testRemoveMethod(): void
    {
        $entity = new Activity();
        $entity->setTitle('Activity to Remove ' . uniqid());
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->getRepository()->save($entity, true);
        $id = $entity->getId();
        $this->assertNotEquals(0, $id);

        $this->getRepository()->remove($entity, true);

        $found = $this->getRepository()->find($id);
        $this->assertNull($found);
    }

    public function testFindAll(): void
    {
        $entity1 = new Activity();
        $entity1->setTitle('Activity One ' . uniqid());
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Activity();
        $entity2->setTitle('Activity Two ' . uniqid());
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $results = $this->getRepository()->findAll();
        $this->assertGreaterThanOrEqual(2, count($results));

        $foundEntity1 = false;
        $foundEntity2 = false;
        foreach ($results as $result) {
            if ($result->getTitle() === $entity1->getTitle()) {
                $foundEntity1 = true;
            }
            if ($result->getTitle() === $entity2->getTitle()) {
                $foundEntity2 = true;
            }
        }
        $this->assertTrue($foundEntity1);
        $this->assertTrue($foundEntity2);
    }

    public function testFindBy(): void
    {
        $uniqueTitle = 'Findby Test Activity ' . uniqid();
        $entity = new Activity();
        $entity->setTitle($uniqueTitle);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setValid(true);

        $this->getRepository()->save($entity, true);

        $results = $this->getRepository()->findBy(['title' => $uniqueTitle]);
        $this->assertCount(1, $results);
        $this->assertEquals($uniqueTitle, $results[0]->getTitle());
        $this->assertTrue($results[0]->isValid());
    }

    public function testFindOneBy(): void
    {
        $uniqueTitle = 'FindOneBy Test Activity ' . uniqid();
        $entity = new Activity();
        $entity->setTitle($uniqueTitle);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setValid(false);

        $this->getRepository()->save($entity, true);

        $result = $this->getRepository()->findOneBy(['title' => $uniqueTitle, 'valid' => false]);
        $this->assertInstanceOf(Activity::class, $result);
        $this->assertEquals($uniqueTitle, $result->getTitle());
        $this->assertFalse($result->isValid());
    }

    public function testFindByWithNullValue(): void
    {
        $entity = new Activity();
        $entity->setTitle('Null Test Activity ' . uniqid());
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setTextRule(null);

        $this->getRepository()->save($entity, true);

        $results = $this->getRepository()->findBy(['textRule' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            if (str_contains($result->getTitle(), 'Null Test Activity')) {
                $found = true;
                $this->assertNull($result->getTextRule());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCount(): void
    {
        $initialCount = $this->getRepository()->count([]);

        $entity = new Activity();
        $entity->setTitle('Count Test Activity ' . uniqid());
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->getRepository()->save($entity, true);

        $newCount = $this->getRepository()->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByValid(): void
    {
        $entity1 = new Activity();
        $entity1->setTitle('Valid Activity ' . uniqid());
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setValid(true);

        $entity2 = new Activity();
        $entity2->setTitle('Invalid Activity ' . uniqid());
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setValid(false);

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $validResults = $this->getRepository()->findBy(['valid' => true]);
        $invalidResults = $this->getRepository()->findBy(['valid' => false]);

        $this->assertGreaterThanOrEqual(1, count($validResults));
        $this->assertGreaterThanOrEqual(1, count($invalidResults));

        foreach ($validResults as $result) {
            $this->assertTrue($result->isValid());
        }

        foreach ($invalidResults as $result) {
            $this->assertFalse($result->isValid());
        }
    }

    public function testFindByOrderBy(): void
    {
        $entity1 = new Activity();
        $entity1->setTitle('Z Last Activity ' . uniqid());
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Activity();
        $entity2->setTitle('A First Activity ' . uniqid());
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $results = $this->getRepository()->findBy([], ['title' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $titles */
        $titles = array_map(fn ($activity) => $activity->getTitle(), $results);
        $sortedTitles = $titles;
        sort($sortedTitles);
        $this->assertEquals($sortedTitles, $titles);
    }

    public function testFindByLimit(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Activity();
            $entity->setTitle('Limit Test Activity ' . $i . ' ' . uniqid());
            $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
            $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
            $this->getRepository()->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->getRepository()->findBy([], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Activity();
            $entity->setTitle('Offset Test Activity ' . $i . ' ' . uniqid());
            $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
            $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
            $this->getRepository()->save($entity, false);
        }
        self::getEntityManager()->flush();

        $allResults = $this->getRepository()->findBy([], ['title' => 'ASC']);
        $offsetResults = $this->getRepository()->findBy([], ['title' => 'ASC'], null, 2);

        if (count($allResults) > 2) {
            $this->assertNotEmpty($offsetResults);
            $this->assertEquals($allResults[2]->getTitle(), $offsetResults[0]->getTitle());
        }
    }

    public function testManyToManyWithPools(): void
    {
        $activity = new Activity();
        $activity->setTitle('Pool Test Activity ' . uniqid());
        $activity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $activity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $pool = new Pool();
        $pool->setTitle('Test Pool ' . uniqid());
        $pool->setValid(true);

        $activity->addPool($pool);

        self::getEntityManager()->persist($pool);
        $this->getRepository()->save($activity, true);

        $this->assertTrue($activity->getPools()->contains($pool));
        $this->assertTrue($pool->getActivities()->contains($activity));
    }

    public function testFindByDateRange(): void
    {
        $startDate = new \DateTimeImmutable('2024-06-01 00:00:00');
        $endDate = new \DateTimeImmutable('2024-06-30 23:59:59');

        $entity = new Activity();
        $entity->setTitle('Date Range Test Activity ' . uniqid());
        $entity->setStartTime($startDate);
        $entity->setEndTime($endDate);

        $this->getRepository()->save($entity, true);

        $queryBuilder = $this->getRepository()->createQueryBuilder('a')
            ->where('a.startTime >= :start')
            ->andWhere('a.endTime <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
        ;

        $results = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            $this->assertInstanceOf(Activity::class, $result);
            if (str_contains($result->getTitle(), 'Date Range Test Activity')) {
                $found = true;
                $this->assertEquals($startDate, $result->getStartTime());
                $this->assertEquals($endDate, $result->getEndTime());
                break;
            }
        }
        $this->assertTrue($found);
    }

    protected function createNewEntity(): object
    {
        $entity = new Activity();
        $entity->setTitle('Test Activity ' . uniqid());
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        return $entity;
    }

    protected function getRepository(): ActivityRepository
    {
        return self::getService(ActivityRepository::class);
    }
}
