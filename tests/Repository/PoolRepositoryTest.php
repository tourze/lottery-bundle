<?php

namespace LotteryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Repository\PoolRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(PoolRepository::class)]
#[RunTestsInSeparateProcesses]
final class PoolRepositoryTest extends AbstractRepositoryTestCase
{
    private PoolRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PoolRepository::class);
    }

    public function testSaveMethod(): void
    {
        $entity = new Pool();
        $entity->setTitle('Test Pool ' . uniqid());
        $entity->setValid(true);

        $this->repository->save($entity, true);

        $this->assertIsInt($entity->getId());
        $this->assertIsString($entity->getTitle());
        $this->assertStringContainsString('Test Pool', $entity->getTitle());
        $this->assertTrue($entity->isValid());
    }

    public function testRemoveMethod(): void
    {
        $entity = new Pool();
        $entity->setTitle('Pool to Remove ' . uniqid());
        $entity->setValid(false);

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsInt($id);

        $this->repository->remove($entity, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindAll(): void
    {
        $entity1 = new Pool();
        $entity1->setTitle('Pool One ' . uniqid());
        $entity1->setValid(true);

        $entity2 = new Pool();
        $entity2->setTitle('Pool Two ' . uniqid());
        $entity2->setValid(false);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findAll();
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
        $uniqueTitle = 'FindBy Test Pool ' . uniqid();
        $entity = new Pool();
        $entity->setTitle($uniqueTitle);
        $entity->setValid(true);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['title' => $uniqueTitle]);
        $this->assertCount(1, $results);
        $this->assertEquals($uniqueTitle, $results[0]->getTitle());
        $this->assertTrue($results[0]->isValid());
    }

    public function testFindOneBy(): void
    {
        $uniqueTitle = 'FindOneBy Test Pool ' . uniqid();
        $entity = new Pool();
        $entity->setTitle($uniqueTitle);
        $entity->setValid(false);

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['title' => $uniqueTitle, 'valid' => false]);
        $this->assertInstanceOf(Pool::class, $result);
        $this->assertEquals($uniqueTitle, $result->getTitle());
        $this->assertFalse($result->isValid());
    }

    public function testCount(): void
    {
        $initialCount = $this->repository->count([]);

        $entity = new Pool();
        $entity->setTitle('Count Test Pool ' . uniqid());
        $entity->setValid(true);

        $this->repository->save($entity, true);

        $newCount = $this->repository->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByValid(): void
    {
        $entity1 = new Pool();
        $entity1->setTitle('Valid Pool ' . uniqid());
        $entity1->setValid(true);

        $entity2 = new Pool();
        $entity2->setTitle('Invalid Pool ' . uniqid());
        $entity2->setValid(false);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $validResults = $this->repository->findBy(['valid' => true]);
        $invalidResults = $this->repository->findBy(['valid' => false]);

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
        $entity1 = new Pool();
        $entity1->setTitle('Z Last Pool ' . uniqid());
        $entity1->setValid(true);

        $entity2 = new Pool();
        $entity2->setTitle('A First Pool ' . uniqid());
        $entity2->setValid(true);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy([], ['title' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $titles */
        $titles = array_map(fn ($pool) => $pool->getTitle(), $results);
        $sortedTitles = $titles;
        sort($sortedTitles);
        $this->assertEquals($sortedTitles, $titles);
    }

    public function testFindByLimit(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Pool();
            $entity->setTitle('Limit Test Pool ' . $i . ' ' . uniqid());
            $entity->setValid(true);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy([], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Pool();
            $entity->setTitle('Offset Test Pool ' . $i . ' ' . uniqid());
            $entity->setValid(true);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $allResults = $this->repository->findBy([], ['title' => 'ASC']);
        $offsetResults = $this->repository->findBy([], ['title' => 'ASC'], null, 2);

        if (count($allResults) > 2) {
            $this->assertNotEmpty($offsetResults);
            $this->assertEquals($allResults[2]->getTitle(), $offsetResults[0]->getTitle());
        }
    }

    public function testOneToManyWithPoolAttributes(): void
    {
        $pool = new Pool();
        $pool->setTitle('Attribute Test Pool ' . uniqid());
        $pool->setValid(true);

        $attribute1 = new PoolAttribute();
        $attribute1->setName('attr1');
        $attribute1->setValue('value1');
        $attribute1->setPool($pool);

        $attribute2 = new PoolAttribute();
        $attribute2->setName('attr2');
        $attribute2->setValue('value2');
        $attribute2->setPool($pool);

        $pool->addPoolAttribute($attribute1);
        $pool->addPoolAttribute($attribute2);

        self::getEntityManager()->persist($attribute1);
        self::getEntityManager()->persist($attribute2);
        $this->repository->save($pool, true);

        $this->assertTrue($pool->getPoolAttributes()->contains($attribute1));
        $this->assertTrue($pool->getPoolAttributes()->contains($attribute2));
        $this->assertEquals($pool, $attribute1->getPool());
        $this->assertEquals($pool, $attribute2->getPool());
    }

    public function testOneToManyWithPrizes(): void
    {
        $pool = new Pool();
        $pool->setTitle('Prize Test Pool ' . uniqid());
        $pool->setValid(true);

        $prize1 = new Prize();
        $prize1->setName('Test Prize 1');
        $prize1->setType('test_type');
        $prize1->setQuantity(100);
        $prize1->setProbability(50);
        $prize1->setPool($pool);
        $prize1->setValid(true);

        $prize2 = new Prize();
        $prize2->setName('Test Prize 2');
        $prize2->setType('test_type');
        $prize2->setQuantity(200);
        $prize2->setProbability(30);
        $prize2->setPool($pool);
        $prize2->setValid(true);

        $pool->addPrize($prize1);
        $pool->addPrize($prize2);

        self::getEntityManager()->persist($prize1);
        self::getEntityManager()->persist($prize2);
        $this->repository->save($pool, true);

        $this->assertTrue($pool->getPrizes()->contains($prize1));
        $this->assertTrue($pool->getPrizes()->contains($prize2));
        $this->assertEquals($pool, $prize1->getPool());
        $this->assertEquals($pool, $prize2->getPool());
    }

    public function testManyToManyWithActivities(): void
    {
        $pool = new Pool();
        $pool->setTitle('Activity Test Pool ' . uniqid());
        $pool->setValid(true);

        $activity1 = new Activity();
        $activity1->setTitle('Test Activity 1 ' . uniqid());
        $activity1->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $activity1->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $activity2 = new Activity();
        $activity2->setTitle('Test Activity 2 ' . uniqid());
        $activity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $activity2->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));

        $pool->addActivity($activity1);
        $pool->addActivity($activity2);

        self::getEntityManager()->persist($activity1);
        self::getEntityManager()->persist($activity2);
        $this->repository->save($pool, true);

        $this->assertTrue($pool->getActivities()->contains($activity1));
        $this->assertTrue($pool->getActivities()->contains($activity2));
        $this->assertTrue($activity1->getPools()->contains($pool));
        $this->assertTrue($activity2->getPools()->contains($pool));
    }

    protected function createNewEntity(): object
    {
        $entity = new Pool();
        $entity->setTitle('Test Pool ' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<Pool>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(PoolRepository::class);
    }
}
