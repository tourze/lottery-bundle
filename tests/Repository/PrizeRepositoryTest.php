<?php

namespace LotteryBundle\Tests\Repository;

use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use LotteryBundle\Repository\PrizeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(PrizeRepository::class)]
#[RunTestsInSeparateProcesses]
final class PrizeRepositoryTest extends AbstractRepositoryTestCase
{
    private function createTestPool(): Pool
    {
        $pool = new Pool();
        $pool->setTitle('Test Pool ' . uniqid());
        $pool->setValid(true);
        self::getEntityManager()->persist($pool);
        self::getEntityManager()->flush();

        return $pool;
    }

    protected function onSetUp(): void
    {
        // 这个方法必须实现，但可以为空
    }

    public function testSaveMethod(): void
    {
        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('Test Prize');
        $entity->setType('test_type');
        $entity->setQuantity(100);
        $entity->setProbability(50);
        $entity->setPool($pool);
        $entity->setValid(true);
        $entity->setValue('10.50');

        $this->getRepository()->save($entity, true);

        $this->assertIsInt($entity->getId());
        $this->assertEquals('Test Prize', $entity->getName());
        $this->assertEquals('test_type', $entity->getType());
        $this->assertEquals(100, $entity->getQuantity());
        $this->assertEquals(50, $entity->getProbability());
        $this->assertEquals($pool, $entity->getPool());
        $this->assertTrue($entity->isValid());
        $this->assertEquals('10.50', $entity->getValue());
    }

    public function testRemoveMethod(): void
    {
        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('Prize to Remove');
        $entity->setType('remove_type');
        $entity->setQuantity(50);
        $entity->setProbability(25);
        $entity->setPool($pool);

        $this->getRepository()->save($entity, true);
        $id = $entity->getId();
        $this->assertIsInt($id);

        $this->getRepository()->remove($entity, true);

        $found = $this->getRepository()->find($id);
        $this->assertNull($found);
    }

    public function testFindAll(): void
    {
        $pool = $this->createTestPool();
        $entity1 = new Prize();
        $entity1->setName('Prize One');
        $entity1->setType('type_one');
        $entity1->setQuantity(100);
        $entity1->setProbability(30);
        $entity1->setPool($pool);

        $entity2 = new Prize();
        $entity2->setName('Prize Two');
        $entity2->setType('type_two');
        $entity2->setQuantity(200);
        $entity2->setProbability(40);
        $entity2->setPool($pool);

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $results = $this->getRepository()->findAll();
        $this->assertGreaterThanOrEqual(2, count($results));

        $foundEntity1 = false;
        $foundEntity2 = false;
        foreach ($results as $result) {
            if ('Prize One' === $result->getName() && 'type_one' === $result->getType()) {
                $foundEntity1 = true;
            }
            if ('Prize Two' === $result->getName() && 'type_two' === $result->getType()) {
                $foundEntity2 = true;
            }
        }
        $this->assertTrue($foundEntity1);
        $this->assertTrue($foundEntity2);
    }

    public function testFindBy(): void
    {
        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('FindBy Test Prize');
        $entity->setType('findby_type');
        $entity->setQuantity(150);
        $entity->setProbability(60);
        $entity->setPool($pool);

        $this->getRepository()->save($entity, true);

        $results = $this->getRepository()->findBy(['name' => 'FindBy Test Prize']);
        $this->assertCount(1, $results);
        $this->assertEquals('FindBy Test Prize', $results[0]->getName());
        $this->assertEquals('findby_type', $results[0]->getType());
    }

    public function testFindOneBy(): void
    {
        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('FindOneBy Test Prize');
        $entity->setType('findoneby_type');
        $entity->setQuantity(75);
        $entity->setProbability(35);
        $entity->setPool($pool);
        $entity->setValid(false);

        $this->getRepository()->save($entity, true);

        $result = $this->getRepository()->findOneBy(['name' => 'FindOneBy Test Prize', 'type' => 'findoneby_type']);
        $this->assertInstanceOf(Prize::class, $result);
        $this->assertEquals('FindOneBy Test Prize', $result->getName());
        $this->assertEquals('findoneby_type', $result->getType());
        $this->assertFalse($result->isValid());
    }

    public function testFindByWithNullValue(): void
    {
        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('Null Test Prize');
        $entity->setType('null_type');
        $entity->setQuantity(25);
        $entity->setProbability(15);
        $entity->setPool($pool);
        $entity->setContent(null);

        $this->getRepository()->save($entity, true);

        $results = $this->getRepository()->findBy(['content' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            if ('Null Test Prize' === $result->getName()) {
                $found = true;
                $this->assertNull($result->getContent());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCount(): void
    {
        $initialCount = $this->getRepository()->count([]);

        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('Count Test Prize');
        $entity->setType('count_type');
        $entity->setQuantity(300);
        $entity->setProbability(70);
        $entity->setPool($pool);

        $this->getRepository()->save($entity, true);

        $newCount = $this->getRepository()->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByPool(): void
    {
        $pool = $this->createTestPool();
        $entity1 = new Prize();
        $entity1->setName('Pool Prize 1');
        $entity1->setType('pool_type1');
        $entity1->setQuantity(100);
        $entity1->setProbability(20);
        $entity1->setPool($pool);

        $entity2 = new Prize();
        $entity2->setName('Pool Prize 2');
        $entity2->setType('pool_type2');
        $entity2->setQuantity(200);
        $entity2->setProbability(30);
        $entity2->setPool($pool);

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $results = $this->getRepository()->findBy(['pool' => $pool]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Pool::class, $result->getPool());
            $this->assertEquals($pool->getId(), $result->getPool()->getId());
        }
    }

    public function testFindByValid(): void
    {
        $pool = $this->createTestPool();
        $entity1 = new Prize();
        $entity1->setName('Valid Prize');
        $entity1->setType('valid_type');
        $entity1->setQuantity(100);
        $entity1->setProbability(40);
        $entity1->setPool($pool);
        $entity1->setValid(true);

        $entity2 = new Prize();
        $entity2->setName('Invalid Prize');
        $entity2->setType('invalid_type');
        $entity2->setQuantity(150);
        $entity2->setProbability(60);
        $entity2->setPool($pool);
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
        $pool = $this->createTestPool();
        $entity1 = new Prize();
        $entity1->setName('Z Last Prize');
        $entity1->setType('last_type');
        $entity1->setQuantity(100);
        $entity1->setProbability(30);
        $entity1->setPool($pool);

        $entity2 = new Prize();
        $entity2->setName('A First Prize');
        $entity2->setType('first_type');
        $entity2->setQuantity(200);
        $entity2->setProbability(40);
        $entity2->setPool($pool);

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $results = $this->getRepository()->findBy(['pool' => $pool], ['name' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $names */
        $names = array_map(fn ($prize) => $prize->getName(), $results);
        $sortedNames = $names;
        sort($sortedNames);
        $this->assertEquals($sortedNames, $names);
    }

    public function testFindByLimit(): void
    {
        $pool = $this->createTestPool();
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Prize();
            $entity->setName('Limit Test Prize ' . $i);
            $entity->setType('limit_type_' . $i);
            $entity->setQuantity(100 + $i);
            $entity->setProbability(10 + $i);
            $entity->setPool($pool);
            $this->getRepository()->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->getRepository()->findBy(['pool' => $pool], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByOffset(): void
    {
        $pool = $this->createTestPool();
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new Prize();
            $entity->setName('Offset Test Prize ' . $i);
            $entity->setType('offset_type_' . $i);
            $entity->setQuantity(100 + $i);
            $entity->setProbability(10 + $i);
            $entity->setPool($pool);
            $this->getRepository()->save($entity, false);
        }
        self::getEntityManager()->flush();

        $allResults = $this->getRepository()->findBy(['pool' => $pool], ['name' => 'ASC']);
        $offsetResults = $this->getRepository()->findBy(['pool' => $pool], ['name' => 'ASC'], null, 2);

        if (count($allResults) > 2) {
            $this->assertNotEmpty($offsetResults);
            $this->assertEquals($allResults[2]->getName(), $offsetResults[0]->getName());
        }
    }

    public function testOneToManyWithStocks(): void
    {
        $pool = $this->createTestPool();
        $prize = new Prize();
        $prize->setName('Stock Test Prize');
        $prize->setType('stock_type');
        $prize->setQuantity(500);
        $prize->setProbability(80);
        $prize->setPool($pool);
        $prize->setValid(true);

        $stock1 = new Stock();
        $stock1->setSn('SN001' . uniqid());
        $stock1->setPrize($prize);

        $stock2 = new Stock();
        $stock2->setSn('SN002' . uniqid());
        $stock2->setPrize($prize);

        $prize->addStock($stock1);
        $prize->addStock($stock2);

        self::getEntityManager()->persist($stock1);
        self::getEntityManager()->persist($stock2);
        $this->getRepository()->save($prize, true);

        $this->assertTrue($prize->getStocks()->contains($stock1));
        $this->assertTrue($prize->getStocks()->contains($stock2));
        $this->assertEquals($prize, $stock1->getPrize());
        $this->assertEquals($prize, $stock2->getPrize());
    }

    public function testFindByType(): void
    {
        $pool = $this->createTestPool();
        $entity1 = new Prize();
        $entity1->setName('Type A Prize');
        $entity1->setType('type_a');
        $entity1->setQuantity(100);
        $entity1->setProbability(30);
        $entity1->setPool($pool);

        $entity2 = new Prize();
        $entity2->setName('Type B Prize');
        $entity2->setType('type_b');
        $entity2->setQuantity(200);
        $entity2->setProbability(40);
        $entity2->setPool($pool);

        $this->getRepository()->save($entity1, true);
        $this->getRepository()->save($entity2, true);

        $typeAResults = $this->getRepository()->findBy(['type' => 'type_a']);
        $typeBResults = $this->getRepository()->findBy(['type' => 'type_b']);

        $this->assertGreaterThanOrEqual(1, count($typeAResults));
        $this->assertGreaterThanOrEqual(1, count($typeBResults));

        foreach ($typeAResults as $result) {
            $this->assertEquals('type_a', $result->getType());
        }

        foreach ($typeBResults as $result) {
            $this->assertEquals('type_b', $result->getType());
        }
    }

    public function testFindByProbabilityRange(): void
    {
        $pool = $this->createTestPool();
        $lowProbPrize = new Prize();
        $lowProbPrize->setName('Low Probability Prize');
        $lowProbPrize->setType('low_prob');
        $lowProbPrize->setQuantity(100);
        $lowProbPrize->setProbability(10);
        $lowProbPrize->setPool($pool);

        $highProbPrize = new Prize();
        $highProbPrize->setName('High Probability Prize');
        $highProbPrize->setType('high_prob');
        $highProbPrize->setQuantity(200);
        $highProbPrize->setProbability(90);
        $highProbPrize->setPool($pool);

        $this->getRepository()->save($lowProbPrize, true);
        $this->getRepository()->save($highProbPrize, true);

        $queryBuilder = $this->getRepository()->createQueryBuilder('p')
            ->where('p.probability >= :minProb')
            ->andWhere('p.probability <= :maxProb')
            ->setParameter('minProb', 80)
            ->setParameter('maxProb', 100)
        ;

        $results = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            $this->assertInstanceOf(Prize::class, $result);
            if ('High Probability Prize' === $result->getName()) {
                $found = true;
                $this->assertEquals(90, $result->getProbability());
                break;
            }
        }
        $this->assertTrue($found);
    }

    protected function createNewEntity(): object
    {
        $pool = $this->createTestPool();
        $entity = new Prize();
        $entity->setName('Test Prize ' . uniqid());
        $entity->setType('test_type');
        $entity->setQuantity(100);
        $entity->setProbability(50);
        $entity->setPool($pool);

        return $entity;
    }

    protected function getRepository(): PrizeRepository
    {
        return self::getService(PrizeRepository::class);
    }
}
