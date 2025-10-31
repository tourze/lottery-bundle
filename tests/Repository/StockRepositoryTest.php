<?php

namespace LotteryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use LotteryBundle\Repository\StockRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(StockRepository::class)]
#[RunTestsInSeparateProcesses]
final class StockRepositoryTest extends AbstractRepositoryTestCase
{
    private StockRepository $repository;

    private Prize $prize;

    private Chance $chance;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(StockRepository::class);

        // 创建测试用的依赖实体
        $pool = new Pool();
        $pool->setTitle('Test Pool ' . uniqid());
        $pool->setValid(true);
        self::getEntityManager()->persist($pool);

        $this->prize = new Prize();
        $this->prize->setName('Test Prize ' . uniqid());
        $this->prize->setType('test_type');
        $this->prize->setQuantity(100);
        $this->prize->setProbability(50);
        $this->prize->setPool($pool);
        $this->prize->setValid(true);
        self::getEntityManager()->persist($this->prize);

        $activity = new Activity();
        $activity->setTitle('Test Activity ' . uniqid());
        $activity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $activity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($activity);

        $this->chance = new Chance();
        $this->chance->setTitle('Test Chance ' . uniqid());
        $this->chance->setActivity($activity);
        $this->chance->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $this->chance->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($this->chance);

        self::getEntityManager()->flush();
    }

    public function testSaveMethod(): void
    {
        $entity = new Stock();
        $entity->setSn('TEST_SN_' . uniqid());
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);

        $this->assertIsInt($entity->getId());
        $sn = $entity->getSn();
        $this->assertIsString($sn);
        $this->assertStringContainsString('TEST_SN_', $sn);
        $this->assertEquals($this->prize, $entity->getPrize());
        $this->assertIsInt($entity->getLockVersion());
    }

    public function testRemoveMethod(): void
    {
        $entity = new Stock();
        $entity->setSn('REMOVE_SN_' . uniqid());
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsInt($id);

        $this->repository->remove($entity, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindAll(): void
    {
        $entity1 = new Stock();
        $entity1->setSn('STOCK_ONE_' . uniqid());
        $entity1->setPrize($this->prize);

        $entity2 = new Stock();
        $entity2->setSn('STOCK_TWO_' . uniqid());
        $entity2->setPrize($this->prize);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(2, count($results));

        $foundEntity1 = false;
        $foundEntity2 = false;
        foreach ($results as $result) {
            $sn = $result->getSn();
            if (null !== $sn && str_contains($sn, 'STOCK_ONE_')) {
                $foundEntity1 = true;
            }
            if (null !== $sn && str_contains($sn, 'STOCK_TWO_')) {
                $foundEntity2 = true;
            }
        }
        $this->assertTrue($foundEntity1);
        $this->assertTrue($foundEntity2);
    }

    public function testFindBy(): void
    {
        $uniqueSn = 'FINDBY_SN_' . uniqid();
        $entity = new Stock();
        $entity->setSn($uniqueSn);
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['sn' => $uniqueSn]);
        $this->assertCount(1, $results);
        $this->assertEquals($uniqueSn, $results[0]->getSn());
        $this->assertEquals($this->prize, $results[0]->getPrize());
    }

    public function testFindOneBy(): void
    {
        $uniqueSn = 'FINDONEBY_SN_' . uniqid();
        $entity = new Stock();
        $entity->setSn($uniqueSn);
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['sn' => $uniqueSn, 'prize' => $this->prize]);
        $this->assertInstanceOf(Stock::class, $result);
        $this->assertEquals($uniqueSn, $result->getSn());
        $this->assertEquals($this->prize, $result->getPrize());
    }

    public function testCount(): void
    {
        $initialCount = $this->repository->count([]);

        $entity = new Stock();
        $entity->setSn('COUNT_SN_' . uniqid());
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);

        $newCount = $this->repository->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByPrize(): void
    {
        $entity1 = new Stock();
        $entity1->setSn('PRIZE_STOCK_1_' . uniqid());
        $entity1->setPrize($this->prize);

        $entity2 = new Stock();
        $entity2->setSn('PRIZE_STOCK_2_' . uniqid());
        $entity2->setPrize($this->prize);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['prize' => $this->prize]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $result) {
            $prize = $result->getPrize();
            $this->assertInstanceOf(Prize::class, $prize);
            $this->assertEquals($this->prize->getId(), $prize->getId());
        }
    }

    public function testFindByChance(): void
    {
        $entity = new Stock();
        $entity->setSn('CHANCE_STOCK_' . uniqid());
        $entity->setPrize($this->prize);
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['chance' => $this->chance]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            $sn = $result->getSn();
            if (null !== $sn && str_contains($sn, 'CHANCE_STOCK_')) {
                $found = true;
                $chance = $result->getChance();
                $this->assertInstanceOf(Chance::class, $chance);
                $this->assertEquals($this->chance->getId(), $chance->getId());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testFindByOrderBy(): void
    {
        $entity1 = new Stock();
        $entity1->setSn('Z_LAST_' . uniqid());
        $entity1->setPrize($this->prize);

        $entity2 = new Stock();
        $entity2->setSn('A_FIRST_' . uniqid());
        $entity2->setPrize($this->prize);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['prize' => $this->prize], ['sn' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $serials */
        $serials = array_map(fn ($stock) => $stock->getSn(), $results);
        $sortedSerials = $serials;
        sort($sortedSerials);
        $this->assertEquals($sortedSerials, $serials);
    }

    public function testFindByLimit(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Stock();
            $entity->setSn('LIMIT_STOCK_' . $i . '_' . uniqid());
            $entity->setPrize($this->prize);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['prize' => $this->prize], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Stock();
            $entity->setSn('OFFSET_STOCK_' . $i . '_' . uniqid());
            $entity->setPrize($this->prize);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $allResults = $this->repository->findBy(['prize' => $this->prize], ['sn' => 'ASC']);
        $offsetResults = $this->repository->findBy(['prize' => $this->prize], ['sn' => 'ASC'], null, 2);

        if (count($allResults) > 2) {
            $this->assertNotEmpty($offsetResults);
            $this->assertEquals($allResults[2]->getSn(), $offsetResults[0]->getSn());
        }
    }

    public function testOptimisticLocking(): void
    {
        $entity = new Stock();
        $entity->setSn('LOCK_TEST_' . uniqid());
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);

        $originalVersion = $entity->getLockVersion();
        $this->assertIsInt($originalVersion);
        $this->assertEquals(1, $originalVersion);

        // 模拟更新操作
        $entity->setChance($this->chance);
        $this->repository->save($entity, true);

        $this->assertGreaterThan($originalVersion, $entity->getLockVersion());
    }

    public function testManyToOneWithPrize(): void
    {
        $entity = new Stock();
        $entity->setSn('PRIZE_RELATION_' . uniqid());
        $entity->setPrize($this->prize);

        $this->repository->save($entity, true);

        $prize = $entity->getPrize();
        $this->assertInstanceOf(Prize::class, $prize);
        $this->assertEquals($this->prize->getId(), $prize->getId());
        $this->assertEquals($this->prize->getName(), $prize->getName());
    }

    public function testManyToOneWithChance(): void
    {
        $entity = new Stock();
        $entity->setSn('CHANCE_RELATION_' . uniqid());
        $entity->setPrize($this->prize);
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $chance = $entity->getChance();
        $this->assertInstanceOf(Chance::class, $chance);
        $this->assertEquals($this->chance->getId(), $chance->getId());
        $this->assertEquals($this->chance->getTitle(), $chance->getTitle());
    }

    public function testFindAvailableStocks(): void
    {
        // 创建已分配和未分配的库存
        $availableStock = new Stock();
        $availableStock->setSn('AVAILABLE_' . uniqid());
        $availableStock->setPrize($this->prize);
        $availableStock->setChance(null);

        $assignedStock = new Stock();
        $assignedStock->setSn('ASSIGNED_' . uniqid());
        $assignedStock->setPrize($this->prize);
        $assignedStock->setChance($this->chance);

        $this->repository->save($availableStock, true);
        $this->repository->save($assignedStock, true);

        // 查找未分配的库存
        $availableResults = $this->repository->findBy(['chance' => null]);
        $assignedResults = $this->repository->findBy(['chance' => $this->chance]);

        $this->assertGreaterThanOrEqual(1, count($availableResults));
        $this->assertGreaterThanOrEqual(1, count($assignedResults));

        $foundAvailable = false;
        $foundAssigned = false;

        foreach ($availableResults as $result) {
            $sn = $result->getSn();
            if (null !== $sn && str_contains($sn, 'AVAILABLE_')) {
                $foundAvailable = true;
                $this->assertNull($result->getChance());
                break;
            }
        }

        foreach ($assignedResults as $result) {
            $sn = $result->getSn();
            if (null !== $sn && str_contains($sn, 'ASSIGNED_')) {
                $foundAssigned = true;
                $this->assertInstanceOf(Chance::class, $result->getChance());
                break;
            }
        }

        $this->assertTrue($foundAvailable);
        $this->assertTrue($foundAssigned);
    }

    public function testFindOneByAssociationPrizeShouldReturnMatchingEntity(): void
    {
        $entity = new Stock();
        $entity->setSn('ASSOC_SINGLE_PRIZE_' . uniqid());
        $entity->setPrize($this->prize);
        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['prize' => $this->prize]);
        $this->assertInstanceOf(Stock::class, $result);
        $prize = $result->getPrize();
        $this->assertInstanceOf(Prize::class, $prize);
        $this->assertEquals($this->prize->getId(), $prize->getId());
    }

    public function testFindWithOptimisticLockWhenVersionMismatchesShouldThrowExceptionOnFlush(): void
    {
        $entity = new Stock();
        $entity->setSn('LOCK_VERSION_TEST_' . uniqid());
        $entity->setPrize($this->prize);
        $this->repository->save($entity, true);

        $id = $entity->getId();
        $this->assertIsInt($id);
        // 使用数据库连接直接更新版本号来模拟并发修改
        $connection = self::getEntityManager()->getConnection();
        $connection->executeStatement(
            'UPDATE lottery_stock SET lock_version = lock_version + 1 WHERE id = ?',
            [$id]
        );

        // 现在尝试修改原实体，其版本号已经过期
        $entity->setSn('MODIFIED_SN_' . uniqid());

        // 应该抛出乐观锁异常
        $this->expectException(OptimisticLockException::class);
        $this->repository->save($entity, true);
    }

    public function testFindWithPessimisticWriteLockShouldReturnEntityAndLockRow(): void
    {
        $entity = new Stock();
        $entity->setSn('PESSIMISTIC_LOCK_TEST_' . uniqid());
        $entity->setPrize($this->prize);
        $this->repository->save($entity, true);

        $id = $entity->getId();
        $this->assertIsInt($id);

        $em = self::getEntityManager();
        $connection = $em->getConnection();
        $result = null;

        $connection->beginTransaction();
        try {
            $result = $em->find(Stock::class, $id, LockMode::PESSIMISTIC_WRITE);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $this->assertInstanceOf(Stock::class, $result);
        $this->assertEquals($id, $result->getId());
    }

    public function testCountByAssociationPrizeShouldReturnCorrectNumber(): void
    {
        // 创建4个属于该Prize的Stock
        for ($i = 1; $i <= 4; ++$i) {
            $entity = new Stock();
            $entity->setSn('COUNT_ASSOC_PRIZE_' . $i . '_' . uniqid());
            $entity->setPrize($this->prize);
            $this->repository->save($entity, false);
        }

        // 创建另一个Prize和2个属于它的Stock
        $anotherPool = new Pool();
        $anotherPool->setTitle('Another Test Pool ' . uniqid());
        $anotherPool->setValid(true);
        self::getEntityManager()->persist($anotherPool);

        $anotherPrize = new Prize();
        $anotherPrize->setName('Another Test Prize ' . uniqid());
        $anotherPrize->setType('other_type');
        $anotherPrize->setQuantity(50);
        $anotherPrize->setProbability(25);
        $anotherPrize->setPool($anotherPool);
        $anotherPrize->setValid(true);
        self::getEntityManager()->persist($anotherPrize);

        for ($i = 1; $i <= 2; ++$i) {
            $entity = new Stock();
            $entity->setSn('COUNT_OTHER_PRIZE_' . $i . '_' . uniqid());
            $entity->setPrize($anotherPrize);
            $this->repository->save($entity, false);
        }

        self::getEntityManager()->flush();

        $count = $this->repository->count(['prize' => $this->prize]);
        $this->assertGreaterThanOrEqual(4, $count);
    }

    protected function createNewEntity(): object
    {
        $entity = new Stock();
        $entity->setSn('TEST_SN_' . uniqid());
        $entity->setPrize($this->prize);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<Stock>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(StockRepository::class);
    }
}
