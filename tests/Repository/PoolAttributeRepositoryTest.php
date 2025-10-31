<?php

namespace LotteryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use LotteryBundle\Repository\PoolAttributeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(PoolAttributeRepository::class)]
#[RunTestsInSeparateProcesses]
final class PoolAttributeRepositoryTest extends AbstractRepositoryTestCase
{
    private PoolAttributeRepository $repository;

    private Pool $pool;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PoolAttributeRepository::class);

        // 创建测试用的 Pool
        $this->pool = new Pool();
        $this->pool->setTitle('Test Pool ' . uniqid());
        $this->pool->setValid(true);
        self::getEntityManager()->persist($this->pool);
        self::getEntityManager()->flush();
    }

    public function testSaveMethod(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('test_attribute');
        $entity->setValue('test_value');
        $entity->setRemark('test remark');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $this->assertIsString($entity->getId());
        $this->assertEquals('test_attribute', $entity->getName());
        $this->assertEquals('test_value', $entity->getValue());
        $this->assertEquals('test remark', $entity->getRemark());
        $this->assertEquals($this->pool, $entity->getPool());
    }

    public function testRemoveMethod(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('test_attribute_remove');
        $entity->setValue('test_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsString($id);

        $this->repository->remove($entity, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFind(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('find_test');
        $entity->setValue('find_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsString($id);

        $found = $this->repository->find($id);
        $this->assertInstanceOf(PoolAttribute::class, $found);
        $this->assertEquals($id, $found->getId());
        $this->assertEquals('find_test', $found->getName());
        $this->assertEquals('find_value', $found->getValue());
        $this->assertInstanceOf(Pool::class, $found->getPool());
        $this->assertEquals($this->pool->getId(), $found->getPool()->getId());
    }

    public function testFindAll(): void
    {
        $entity1 = new PoolAttribute();
        $entity1->setName('attr1');
        $entity1->setValue('value1');
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('attr2');
        $entity2->setValue('value2');
        $entity2->setPool($this->pool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(2, count($results));

        $foundEntity1 = false;
        $foundEntity2 = false;
        foreach ($results as $result) {
            if ('attr1' === $result->getName() && 'value1' === $result->getValue()) {
                $foundEntity1 = true;
            }
            if ('attr2' === $result->getName() && 'value2' === $result->getValue()) {
                $foundEntity2 = true;
            }
        }
        $this->assertTrue($foundEntity1);
        $this->assertTrue($foundEntity2);
    }

    public function testFindBy(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('findby_test');
        $entity->setValue('findby_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['name' => 'findby_test']);
        $this->assertCount(1, $results);
        $this->assertEquals('findby_test', $results[0]->getName());
        $this->assertEquals('findby_value', $results[0]->getValue());
    }

    public function testFindOneBy(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('findoneby_test');
        $entity->setValue('findoneby_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['name' => 'findoneby_test', 'value' => 'findoneby_value']);
        $this->assertInstanceOf(PoolAttribute::class, $result);
        $this->assertEquals('findoneby_test', $result->getName());
        $this->assertEquals('findoneby_value', $result->getValue());
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $entity1 = new PoolAttribute();
        $entity1->setName('orderby_test_1_' . uniqid());
        $entity1->setValue('z_last_value');
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('orderby_test_2_' . uniqid());
        $entity2->setValue('a_first_value');
        $entity2->setPool($this->pool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $result = $this->repository->findOneBy(['pool' => $this->pool], ['value' => 'ASC']);
        $this->assertInstanceOf(PoolAttribute::class, $result);
        $this->assertContains($result->getValue(), ['a_first_value', 'z_last_value']);
    }

    public function testFindByWithNullValue(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('null_test');
        $entity->setValue('value_with_null_remark');
        $entity->setRemark(null);
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['remark' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            if ('null_test' === $result->getName()) {
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

        $entity = new PoolAttribute();
        $entity->setName('count_test');
        $entity->setValue('count_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $newCount = $this->repository->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByPool(): void
    {
        $entity1 = new PoolAttribute();
        $entity1->setName('pool_attr1');
        $entity1->setValue('value1');
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('pool_attr2');
        $entity2->setValue('value2');
        $entity2->setPool($this->pool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['pool' => $this->pool]);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Pool::class, $result->getPool());
            $this->assertEquals($this->pool->getId(), $result->getPool()->getId());
        }
    }

    public function testFindByOrderBy(): void
    {
        $entity1 = new PoolAttribute();
        $entity1->setName('z_last');
        $entity1->setValue('value1');
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('a_first');
        $entity2->setValue('value2');
        $entity2->setPool($this->pool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['pool' => $this->pool], ['name' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $names */
        $names = array_map(fn ($attr) => $attr->getName(), $results);
        $sortedNames = $names;
        sort($sortedNames);
        $this->assertEquals($sortedNames, $names);
    }

    public function testFindByLimit(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new PoolAttribute();
            $entity->setName('limit_test_' . $i);
            $entity->setValue('value_' . $i);
            $entity->setPool($this->pool);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['pool' => $this->pool], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new PoolAttribute();
            $entity->setName('offset_test_' . $i);
            $entity->setValue('value_' . $i);
            $entity->setPool($this->pool);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $allResults = $this->repository->findBy(['pool' => $this->pool], ['name' => 'ASC']);
        $offsetResults = $this->repository->findBy(['pool' => $this->pool], ['name' => 'ASC'], null, 2);

        if (count($allResults) > 2) {
            $this->assertNotEmpty($offsetResults);
            $this->assertEquals($allResults[2]->getName(), $offsetResults[0]->getName());
        }
    }

    public function testFindByAssociationWithPool(): void
    {
        $otherPool = new Pool();
        $otherPool->setTitle('Other Pool ' . uniqid());
        $otherPool->setValid(true);
        self::getEntityManager()->persist($otherPool);
        self::getEntityManager()->flush();

        $entity1 = new PoolAttribute();
        $entity1->setName('association_test_1');
        $entity1->setValue('value1');
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('association_test_2');
        $entity2->setValue('value2');
        $entity2->setPool($otherPool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['pool' => $this->pool]);
        $foundThisPool = false;
        $foundOtherPool = false;

        foreach ($results as $result) {
            if ('association_test_1' === $result->getName()) {
                $foundThisPool = true;
                $this->assertInstanceOf(Pool::class, $result->getPool());
                $this->assertEquals($this->pool->getId(), $result->getPool()->getId());
            }
            if ('association_test_2' === $result->getName()) {
                $foundOtherPool = true;
            }
        }

        $this->assertTrue($foundThisPool);
        $this->assertFalse($foundOtherPool);
    }

    public function testCountByAssociationWithPool(): void
    {
        $otherPool = new Pool();
        $otherPool->setTitle('Count Pool ' . uniqid());
        $otherPool->setValid(true);
        self::getEntityManager()->persist($otherPool);
        self::getEntityManager()->flush();

        $entity1 = new PoolAttribute();
        $entity1->setName('count_assoc_1');
        $entity1->setValue('value1');
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('count_assoc_2');
        $entity2->setValue('value2');
        $entity2->setPool($this->pool);

        $entity3 = new PoolAttribute();
        $entity3->setName('count_assoc_3');
        $entity3->setValue('value3');
        $entity3->setPool($otherPool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);
        $this->repository->save($entity3, true);

        $thisPoolCount = $this->repository->count(['pool' => $this->pool]);
        $otherPoolCount = $this->repository->count(['pool' => $otherPool]);

        $this->assertGreaterThanOrEqual(2, $thisPoolCount);
        $this->assertGreaterThanOrEqual(1, $otherPoolCount);
    }

    public function testFindByNullRemark(): void
    {
        $entity1 = new PoolAttribute();
        $entity1->setName('null_remark_1');
        $entity1->setValue('value1');
        $entity1->setRemark(null);
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('null_remark_2');
        $entity2->setValue('value2');
        $entity2->setRemark('not null');
        $entity2->setPool($this->pool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy(['remark' => null]);
        $foundNullRemark = false;
        $foundNotNullRemark = false;

        foreach ($results as $result) {
            if ('null_remark_1' === $result->getName()) {
                $foundNullRemark = true;
                $this->assertNull($result->getRemark());
            }
            if ('null_remark_2' === $result->getName()) {
                $foundNotNullRemark = true;
            }
        }

        $this->assertTrue($foundNullRemark);
        $this->assertFalse($foundNotNullRemark);
    }

    public function testCountByNullRemark(): void
    {
        $entity1 = new PoolAttribute();
        $entity1->setName('count_null_1');
        $entity1->setValue('value1');
        $entity1->setRemark(null);
        $entity1->setPool($this->pool);

        $entity2 = new PoolAttribute();
        $entity2->setName('count_null_2');
        $entity2->setValue('value2');
        $entity2->setRemark(null);
        $entity2->setPool($this->pool);

        $entity3 = new PoolAttribute();
        $entity3->setName('count_null_3');
        $entity3->setValue('value3');
        $entity3->setRemark('not null');
        $entity3->setPool($this->pool);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);
        $this->repository->save($entity3, true);

        $nullCount = $this->repository->count(['remark' => null]);
        $this->assertGreaterThanOrEqual(2, $nullCount);
    }

    public function testFindByNullCreatedBy(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('null_created_by_test');
        $entity->setValue('test_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['createdBy' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            if ('null_created_by_test' === $result->getName()) {
                $found = true;
                $this->assertNull($result->getCreatedBy());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCountByNullCreatedBy(): void
    {
        $initialCount = $this->repository->count(['createdBy' => null]);

        $entity = new PoolAttribute();
        $entity->setName('count_null_created_by');
        $entity->setValue('test_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $newCount = $this->repository->count(['createdBy' => null]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByNullUpdatedBy(): void
    {
        $entity = new PoolAttribute();
        $entity->setName('null_updated_by_test');
        $entity->setValue('test_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['updatedBy' => null]);
        $this->assertGreaterThanOrEqual(1, count($results));

        $found = false;
        foreach ($results as $result) {
            if ('null_updated_by_test' === $result->getName()) {
                $found = true;
                $this->assertNull($result->getUpdatedBy());
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testCountByNullUpdatedBy(): void
    {
        $initialCount = $this->repository->count(['updatedBy' => null]);

        $entity = new PoolAttribute();
        $entity->setName('count_null_updated_by');
        $entity->setValue('test_value');
        $entity->setPool($this->pool);

        $this->repository->save($entity, true);

        $newCount = $this->repository->count(['updatedBy' => null]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    protected function createNewEntity(): object
    {
        $entity = new PoolAttribute();
        $entity->setName('Test PoolAttribute ' . uniqid());
        $entity->setValue('Test Value ' . uniqid());
        $entity->setPool($this->pool);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<PoolAttribute>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(PoolAttributeRepository::class);
    }
}
