<?php

namespace LotteryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Repository\ChanceRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ChanceRepository::class)]
#[RunTestsInSeparateProcesses]
final class ChanceRepositoryTest extends AbstractRepositoryTestCase
{
    private ChanceRepository $repository;

    private Activity $activity;

    private Pool $pool;

    private Prize $prize;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ChanceRepository::class);

        // 创建测试用的 Activity
        $this->activity = new Activity();
        $this->activity->setTitle('Test Activity ' . uniqid());
        $this->activity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $this->activity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($this->activity);

        // 创建测试用的 Pool 和 Prize
        $this->pool = new Pool();
        $this->pool->setTitle('Test Pool ' . uniqid());
        $this->pool->setValid(true);
        self::getEntityManager()->persist($this->pool);

        $this->prize = new Prize();
        $this->prize->setName('Test Prize ' . uniqid());
        $this->prize->setType('test_type');
        $this->prize->setQuantity(100);
        $this->prize->setProbability(50);
        $this->prize->setPool($this->pool);
        $this->prize->setValid(true);
        self::getEntityManager()->persist($this->prize);

        self::getEntityManager()->flush();
    }

    public function testSaveMethod(): void
    {
        $entity = new Chance();
        $entity->setTitle('Test Chance');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setValid(true);
        $entity->setStatus(ChanceStatusEnum::INIT);

        $this->repository->save($entity, true);

        $this->assertIsInt($entity->getId());
        $this->assertEquals('Test Chance', $entity->getTitle());
        $this->assertTrue($entity->getValid());
        $this->assertEquals(ChanceStatusEnum::INIT, $entity->getStatus());
        $this->assertIsInt($entity->getLockVersion());
    }

    public function testRemoveMethod(): void
    {
        $entity = new Chance();
        $entity->setTitle('Chance to Remove');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsInt($id);

        $this->repository->remove($entity, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindAll(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Chance One');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle('Chance Two');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(2, count($results));

        $foundEntity1 = false;
        $foundEntity2 = false;
        foreach ($results as $result) {
            if ('Chance One' === $result->getTitle()) {
                $foundEntity1 = true;
            }
            if ('Chance Two' === $result->getTitle()) {
                $foundEntity2 = true;
            }
        }
        $this->assertTrue($foundEntity1);
        $this->assertTrue($foundEntity2);
    }

    public function testFindBy(): void
    {
        $entity = new Chance();
        $entity->setTitle('FindBy Test Chance');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setValid(true);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['title' => 'FindBy Test Chance']);
        $this->assertCount(1, $results);
        $this->assertEquals('FindBy Test Chance', $results[0]->getTitle());
        $this->assertTrue($results[0]->getValid());
    }

    public function testFindOneBy(): void
    {
        $entity = new Chance();
        $entity->setTitle('FindOneBy Test Chance');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setStatus(ChanceStatusEnum::WINNING);

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['title' => 'FindOneBy Test Chance', 'status' => ChanceStatusEnum::WINNING]);
        $this->assertInstanceOf(Chance::class, $result);
        $this->assertEquals('FindOneBy Test Chance', $result->getTitle());
        $this->assertEquals(ChanceStatusEnum::WINNING, $result->getStatus());
    }

    public function testFindByWithNullValue(): void
    {
        $entity = new Chance();
        $entity->setTitle('Null Test Chance');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setRemark(null);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['remark' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            if ('Null Test Chance' === $result->getTitle()) {
                $found = true;
                $this->assertNull($result->getRemark());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCount(): void
    {
        $initialCount = $this->repository->count([]);

        $entity = new Chance();
        $entity->setTitle('Count Test Chance');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $newCount = $this->repository->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByActivity(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Activity Chance 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle('Activity Chance 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['activity' => $this->activity]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Activity::class, $result->getActivity());
            $this->assertEquals($this->activity->getId(), $result->getActivity()->getId());
        }
    }

    public function testFindByStatus(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Init Chance');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setStatus(ChanceStatusEnum::INIT);

        $entity2 = new Chance();
        $entity2->setTitle('Used Chance');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setStatus(ChanceStatusEnum::WINNING);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $initResults = $this->repository->findBy(['status' => ChanceStatusEnum::INIT]);
        $winningResults = $this->repository->findBy(['status' => ChanceStatusEnum::WINNING]);

        $this->assertGreaterThanOrEqual(1, count($initResults));
        $this->assertGreaterThanOrEqual(1, count($winningResults));

        foreach ($initResults as $result) {
            $this->assertEquals(ChanceStatusEnum::INIT, $result->getStatus());
        }

        foreach ($winningResults as $result) {
            $this->assertEquals(ChanceStatusEnum::WINNING, $result->getStatus());
        }
    }

    public function testFindByOrderBy(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Z Last Chance');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle('A First Chance');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['activity' => $this->activity], ['title' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $titles */
        $titles = array_map(fn ($chance) => $chance->getTitle(), $results);
        $sortedTitles = $titles;
        sort($sortedTitles);
        $this->assertEquals($sortedTitles, $titles);
    }

    public function testFindByLimit(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Chance();
            $entity->setTitle('Limit Test Chance ' . $i);
            $entity->setActivity($this->activity);
            $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
            $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['activity' => $this->activity], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Chance();
            $entity->setTitle('Offset Test Chance ' . $i);
            $entity->setActivity($this->activity);
            $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
            $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $allResults = $this->repository->findBy(['activity' => $this->activity], ['title' => 'ASC']);
        $offsetResults = $this->repository->findBy(['activity' => $this->activity], ['title' => 'ASC'], null, 2);

        if (count($allResults) > 2) {
            $this->assertNotEmpty($offsetResults);
            $this->assertEquals($allResults[2]->getTitle(), $offsetResults[0]->getTitle());
        }
    }

    public function testAssociationWithPool(): void
    {
        $entity = new Chance();
        $entity->setTitle('Pool Association Test');
        $entity->setActivity($this->activity);
        $entity->setPool($this->pool);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $this->assertInstanceOf(Pool::class, $entity->getPool());
        $this->assertEquals($this->pool->getId(), $entity->getPool()->getId());
        $this->assertEquals($this->pool->getTitle(), $entity->getPool()->getTitle());
    }

    public function testAssociationWithPrize(): void
    {
        $entity = new Chance();
        $entity->setTitle('Prize Association Test');
        $entity->setActivity($this->activity);
        $entity->setPrize($this->prize);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $this->assertInstanceOf(Prize::class, $entity->getPrize());
        $this->assertEquals($this->prize->getId(), $entity->getPrize()->getId());
        $this->assertEquals($this->prize->getName(), $entity->getPrize()->getName());
    }

    public function testOptimisticLocking(): void
    {
        $entity = new Chance();
        $entity->setTitle('Optimistic Lock Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $originalVersion = $entity->getLockVersion();
        $this->assertIsInt($originalVersion);

        $entity->setTitle('Updated Title');
        $this->repository->save($entity, true);

        $this->assertGreaterThan($originalVersion, $entity->getLockVersion());
    }

    public function testFindByDateRange(): void
    {
        $startDate = new \DateTimeImmutable('2024-06-01 00:00:00');
        $expireDate = new \DateTimeImmutable('2024-06-30 23:59:59');

        $entity = new Chance();
        $entity->setTitle('Date Range Test Chance');
        $entity->setActivity($this->activity);
        $entity->setStartTime($startDate);
        $entity->setExpireTime($expireDate);

        $this->repository->save($entity, true);

        $queryBuilder = $this->repository->createQueryBuilder('c')
            ->where('c.startTime >= :start')
            ->andWhere('c.expireTime <= :expire')
            ->setParameter('start', $startDate)
            ->setParameter('expire', $expireDate)
        ;

        $results = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            $this->assertInstanceOf(Chance::class, $result);
            if ('Date Range Test Chance' === $result->getTitle()) {
                $found = true;
                $this->assertEquals($startDate, $result->getStartTime());
                $this->assertEquals($expireDate, $result->getExpireTime());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('FindOneBy Order 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setStatus(ChanceStatusEnum::INIT);

        $entity2 = new Chance();
        $entity2->setTitle('FindOneBy Order 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setStatus(ChanceStatusEnum::INIT);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $result = $this->repository->findOneBy(['status' => ChanceStatusEnum::INIT], ['title' => 'ASC']);
        $this->assertInstanceOf(Chance::class, $result);
        $this->assertEquals(ChanceStatusEnum::INIT, $result->getStatus());
    }

    public function testFindWithOptimisticLockWhenVersionMismatchesShouldThrowExceptionOnFlush(): void
    {
        $entity = new Chance();
        $entity->setTitle('Optimistic Lock Conflict Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsInt($id);

        // Simulate concurrent modification by directly updating the version
        $conn = self::getEntityManager()->getConnection();
        $conn->executeStatement(
            'UPDATE lottery_chance SET lock_version = lock_version + 1 WHERE id = ?',
            [$id]
        );

        // Try to update the stale entity
        $entity->setTitle('Updated Title');

        $this->expectException(OptimisticLockException::class);
        $this->repository->save($entity, true);
    }

    public function testFindWithPessimisticWriteLockShouldReturnEntityAndLockRow(): void
    {
        $entity = new Chance();
        $entity->setTitle('Pessimistic Lock Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);
        $id = $entity->getId();

        self::getEntityManager()->beginTransaction();
        try {
            $lockedEntity = $this->repository->find($id, LockMode::PESSIMISTIC_WRITE);
            $this->assertInstanceOf(Chance::class, $lockedEntity);
            $this->assertEquals($id, $lockedEntity->getId());
            $this->assertEquals('Pessimistic Lock Test', $lockedEntity->getTitle());

            self::getEntityManager()->commit();
        } catch (\Exception $e) {
            self::getEntityManager()->rollback();
            throw $e;
        }
    }

    public function testCountWithAssociationCriteria(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Association Count Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setPool($this->pool);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle('Association Count Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setPool($this->pool);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $count = $this->repository->count(['activity' => $this->activity]);
        $this->assertGreaterThanOrEqual(2, $count);

        $poolCount = $this->repository->count(['pool' => $this->pool]);
        $this->assertGreaterThanOrEqual(2, $poolCount);

        $prizeCount = $this->repository->count(['prize' => $this->prize]);
        $this->assertEquals(0, $prizeCount); // No entities with this prize yet
    }

    public function testFindByWithAssociationCriteria(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Association Query Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setPool($this->pool);
        $entity1->setPrize($this->prize);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle('Association Query Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setPool($this->pool);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $activityResults = $this->repository->findBy(['activity' => $this->activity]);
        $this->assertGreaterThanOrEqual(2, count($activityResults));

        $poolResults = $this->repository->findBy(['pool' => $this->pool]);
        $this->assertGreaterThanOrEqual(2, count($poolResults));

        $prizeResults = $this->repository->findBy(['prize' => $this->prize]);
        $this->assertGreaterThanOrEqual(1, count($prizeResults));
        $this->assertEquals('Association Query Test 1', $prizeResults[0]->getTitle());
    }

    public function testFindOneByWithAssociationCriteria(): void
    {
        $entity = new Chance();
        $entity->setTitle('Association FindOneBy Test');
        $entity->setActivity($this->activity);
        $entity->setPool($this->pool);
        $entity->setPrize($this->prize);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['activity' => $this->activity, 'pool' => $this->pool]);
        $this->assertInstanceOf(Chance::class, $result);
        $this->assertInstanceOf(Activity::class, $result->getActivity());
        $this->assertEquals($this->activity->getId(), $result->getActivity()->getId());
        $this->assertInstanceOf(Pool::class, $result->getPool());
        $this->assertEquals($this->pool->getId(), $result->getPool()->getId());

        $prizeResult = $this->repository->findOneBy(['prize' => $this->prize]);
        $this->assertInstanceOf(Chance::class, $prizeResult);
        $this->assertInstanceOf(Prize::class, $prizeResult->getPrize());
        $this->assertEquals($this->prize->getId(), $prizeResult->getPrize()->getId());
    }

    public function testFindByWithNullValueForNullableFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Null Field Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setRemark(null);
        $entity1->setUseTime(null);

        $entity2 = new Chance();
        $entity2->setTitle('Null Field Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setRemark('Some remark');
        $entity2->setUseTime(new \DateTimeImmutable('2024-06-01 12:00:00'));

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullRemarkResults = $this->repository->findBy(['remark' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullRemarkResults));

        $foundNullRemark = false;
        foreach ($nullRemarkResults as $result) {
            if ('Null Field Test 1' === $result->getTitle()) {
                $foundNullRemark = true;
                $this->assertNull($result->getRemark());
                break;
            }
        }
        $this->assertTrue($foundNullRemark);

        $nullUseTimeResults = $this->repository->findBy(['useTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullUseTimeResults));

        $foundNullUseTime = false;
        foreach ($nullUseTimeResults as $result) {
            if ('Null Field Test 1' === $result->getTitle()) {
                $foundNullUseTime = true;
                $this->assertNull($result->getUseTime());
                break;
            }
        }
        $this->assertTrue($foundNullUseTime);
    }

    public function testFindOneByWithNullValueForNullableFields(): void
    {
        $entity = new Chance();
        $entity->setTitle('FindOneBy Null Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity->setRemark(null);
        $entity->setSendTime(null);

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['title' => 'FindOneBy Null Test', 'remark' => null]);
        $this->assertInstanceOf(Chance::class, $result);
        $this->assertEquals('FindOneBy Null Test', $result->getTitle());
        $this->assertNull($result->getRemark());

        $sendTimeResult = $this->repository->findOneBy(['title' => 'FindOneBy Null Test', 'sendTime' => null]);
        $this->assertInstanceOf(Chance::class, $sendTimeResult);
        $this->assertEquals('FindOneBy Null Test', $sendTimeResult->getTitle());
        $this->assertNull($sendTimeResult->getSendTime());
    }

    public function testCountWithNullValueForNullableFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Count Null Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setRemark(null);

        $entity2 = new Chance();
        $entity2->setTitle('Count Null Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setRemark(null);

        $entity3 = new Chance();
        $entity3->setTitle('Count Non-Null Test');
        $entity3->setActivity($this->activity);
        $entity3->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity3->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity3->setRemark('Non-null remark');

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, false);
        $this->repository->save($entity3, true);

        $nullRemarkCount = $this->repository->count(['remark' => null]);
        $this->assertGreaterThanOrEqual(2, $nullRemarkCount);

        $nonNullRemarkCount = $this->repository->count(['remark' => 'Non-null remark']);
        $this->assertGreaterThanOrEqual(1, $nonNullRemarkCount);
    }

    public function testCountWithUserAssociation(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('User Association Count Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle('User Association Count Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullUserCount = $this->repository->count(['user' => null]);
        $this->assertGreaterThanOrEqual(2, $nullUserCount);
    }

    public function testFindByWithUserAssociation(): void
    {
        $entity = new Chance();
        $entity->setTitle('User Association FindBy Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $nullUserResults = $this->repository->findBy(['user' => null]);
        $this->assertIsArray($nullUserResults);
        $this->assertGreaterThanOrEqual(1, count($nullUserResults));
    }

    public function testFindOneByWithUserAssociation(): void
    {
        $entity = new Chance();
        $entity->setTitle('User Association FindOneBy Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $nullUserResult = $this->repository->findOneBy(['user' => null]);
        if (null !== $nullUserResult) {
            $this->assertNull($nullUserResult->getUser());
        }
    }

    public function testCountWithReviewUserAssociation(): void
    {
        $entity = new Chance();
        $entity->setTitle('Review User Count Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $nullReviewUserCount = $this->repository->count(['reviewUser' => null]);
        $this->assertGreaterThanOrEqual(1, $nullReviewUserCount);
    }

    public function testFindByWithReviewUserAssociation(): void
    {
        $entity = new Chance();
        $entity->setTitle('Review User FindBy Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $reviewUserResults = $this->repository->findBy(['reviewUser' => null]);
        $this->assertIsArray($reviewUserResults);
        $this->assertGreaterThanOrEqual(1, count($reviewUserResults));
    }

    public function testFindOneByWithReviewUserAssociation(): void
    {
        $entity = new Chance();
        $entity->setTitle('Review User FindOneBy Test');
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['reviewUser' => null]);
        if (null !== $result) {
            $this->assertNull($result->getReviewUser());
        }
    }

    public function testFindByWithNullableTimestampFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Null Timestamp Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setSendTime(null);
        $entity1->setUseTime(null);

        $entity2 = new Chance();
        $entity2->setTitle('Null Timestamp Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setSendTime(new \DateTimeImmutable('2024-06-01 10:00:00'));
        $entity2->setUseTime(new \DateTimeImmutable('2024-06-01 11:00:00'));

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullSendTimeResults = $this->repository->findBy(['sendTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullSendTimeResults));

        $nullUseTimeResults = $this->repository->findBy(['useTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullUseTimeResults));
    }

    public function testCountWithNullableTimestampFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Count Null Timestamp 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setSendTime(null);

        $entity2 = new Chance();
        $entity2->setTitle('Count Null Timestamp 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setSendTime(null);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullSendTimeCount = $this->repository->count(['sendTime' => null]);
        $this->assertGreaterThanOrEqual(2, $nullSendTimeCount);

        $nullUseTimeCount = $this->repository->count(['useTime' => null]);
        $this->assertGreaterThanOrEqual(2, $nullUseTimeCount);
    }

    public function testFindByWithNullableStringFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Null String Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setReviewTime(null);

        $entity2 = new Chance();
        $entity2->setTitle('Null String Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setReviewTime('2024-06-01 15:30:00');

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullReviewTimeResults = $this->repository->findBy(['reviewTime' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullReviewTimeResults));

        $nonNullReviewTimeResults = $this->repository->findBy(['reviewTime' => '2024-06-01 15:30:00']);
        $this->assertGreaterThanOrEqual(1, count($nonNullReviewTimeResults));
    }

    public function testCountWithNullableStringFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Count Null String 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setReviewTime(null);

        $entity2 = new Chance();
        $entity2->setTitle('Count Null String 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setReviewTime(null);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullReviewTimeCount = $this->repository->count(['reviewTime' => null]);
        $this->assertGreaterThanOrEqual(2, $nullReviewTimeCount);
    }

    public function testFindOneByWithOrderByShouldUseOrderingCorrectly(): void
    {
        $uniquePrefix = 'OrderTest' . uniqid() . '_';

        $entity1 = new Chance();
        $entity1->setTitle($uniquePrefix . 'A Order Test Entity');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setStatus(ChanceStatusEnum::INIT);

        $entity2 = new Chance();
        $entity2->setTitle($uniquePrefix . 'Z Order Test Entity');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setStatus(ChanceStatusEnum::INIT);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        // Test ASC ordering - using criteria that will uniquely identify our test data
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.title LIKE :prefix')
            ->andWhere('c.status = :status')
            ->setParameter('prefix', $uniquePrefix . '%')
            ->setParameter('status', ChanceStatusEnum::INIT)
            ->orderBy('c.title', 'ASC')
            ->setMaxResults(1)
        ;

        $ascResult = $qb->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(Chance::class, $ascResult);
        $this->assertEquals($uniquePrefix . 'A Order Test Entity', $ascResult->getTitle());

        // Test DESC ordering
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.title LIKE :prefix')
            ->andWhere('c.status = :status')
            ->setParameter('prefix', $uniquePrefix . '%')
            ->setParameter('status', ChanceStatusEnum::INIT)
            ->orderBy('c.title', 'DESC')
            ->setMaxResults(1)
        ;

        $descResult = $qb->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(Chance::class, $descResult);
        $this->assertEquals($uniquePrefix . 'Z Order Test Entity', $descResult->getTitle());
    }

    public function testFindByWithTitleNullField(): void
    {
        // Create entity with null title to test nullable title field
        $entity = new Chance();
        $entity->setTitle(null);
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity, true);

        $nullTitleResults = $this->repository->findBy(['title' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullTitleResults));
    }

    public function testCountWithTitleNullField(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle(null);
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $entity2 = new Chance();
        $entity2->setTitle(null);
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullTitleCount = $this->repository->count(['title' => null]);
        $this->assertGreaterThanOrEqual(2, $nullTitleCount);
    }

    public function testFindByWithNullableJsonFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Null JSON Test 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setSendResult(null);
        $entity1->setPoolContext(null);
        $entity1->setProbabilityContext(null);

        $entity2 = new Chance();
        $entity2->setTitle('Null JSON Test 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setSendResult(['status' => 'sent']);
        $entity2->setPoolContext(['pool_id' => 1]);
        $entity2->setProbabilityContext([['id' => 1, 'name' => 'Test Prize', 'rate' => 50]]);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullSendResultResults = $this->repository->findBy(['sendResult' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullSendResultResults));

        $nullPoolContextResults = $this->repository->findBy(['poolContext' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullPoolContextResults));

        $nullProbabilityContextResults = $this->repository->findBy(['probabilityContext' => null]);
        $this->assertGreaterThanOrEqual(1, count($nullProbabilityContextResults));
    }

    public function testCountWithNullableJsonFields(): void
    {
        $entity1 = new Chance();
        $entity1->setTitle('Count Null JSON 1');
        $entity1->setActivity($this->activity);
        $entity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity1->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity1->setSendResult(null);

        $entity2 = new Chance();
        $entity2->setTitle('Count Null JSON 2');
        $entity2->setActivity($this->activity);
        $entity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        $entity2->setSendResult(null);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, true);

        $nullSendResultCount = $this->repository->count(['sendResult' => null]);
        $this->assertGreaterThanOrEqual(2, $nullSendResultCount);

        $nullPoolContextCount = $this->repository->count(['poolContext' => null]);
        $this->assertGreaterThanOrEqual(0, $nullPoolContextCount);

        $nullProbabilityContextCount = $this->repository->count(['probabilityContext' => null]);
        $this->assertGreaterThanOrEqual(0, $nullProbabilityContextCount);
    }

    protected function createNewEntity(): object
    {
        $entity = new Chance();
        $entity->setTitle('Test Chance ' . uniqid());
        $entity->setActivity($this->activity);
        $entity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $entity->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<Chance>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ChanceRepository::class);
    }
}
