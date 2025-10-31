<?php

namespace LotteryBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Repository\ConsigneeRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ConsigneeRepository::class)]
#[RunTestsInSeparateProcesses]
final class ConsigneeRepositoryTest extends AbstractRepositoryTestCase
{
    private ConsigneeRepository $repository;

    private Chance $chance;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ConsigneeRepository::class);

        // 创建测试用的 Activity 和 Chance
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
        $entity = new Consignee();
        $entity->setRealName('张三');
        $entity->setMobile('13800138000');
        $entity->setAddress('北京市朝阳区测试地址123号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $this->assertIsInt($entity->getId());
        $this->assertEquals('张三', $entity->getRealName());
        $this->assertEquals('13800138000', $entity->getMobile());
        $this->assertEquals('北京市朝阳区测试地址123号', $entity->getAddress());
        $this->assertInstanceOf(Chance::class, $entity->getChance());
        $this->assertEquals($this->chance->getId(), $entity->getChance()->getId());
    }

    public function testRemoveMethod(): void
    {
        $entity = new Consignee();
        $entity->setRealName('李四');
        $entity->setMobile('13900139000');
        $entity->setAddress('上海市浦东新区测试路456号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);
        $id = $entity->getId();
        $this->assertIsInt($id);

        $this->repository->remove($entity, true);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindAll(): void
    {
        $entity1 = new Consignee();
        $entity1->setRealName('王五');
        $entity1->setMobile('13700137000');
        $entity1->setAddress('广州市天河区测试街789号');
        $entity1->setChance($this->chance);

        // 创建另一个 Chance 用于第二个 Consignee
        $activity2 = new Activity();
        $activity2->setTitle('Test Activity2 ' . uniqid());
        $activity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $activity2->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($activity2);

        $chance2 = new Chance();
        $chance2->setTitle('Test Chance2 ' . uniqid());
        $chance2->setActivity($activity2);
        $chance2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $chance2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($chance2);
        self::getEntityManager()->flush();

        $entity2 = new Consignee();
        $entity2->setRealName('赵六');
        $entity2->setMobile('15800158000');
        $entity2->setAddress('深圳市南山区测试大道321号');
        $entity2->setChance($chance2);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findAll();
        $this->assertGreaterThanOrEqual(2, count($results));

        $foundEntity1 = false;
        $foundEntity2 = false;
        foreach ($results as $result) {
            if ('王五' === $result->getRealName() && '13700137000' === $result->getMobile()) {
                $foundEntity1 = true;
            }
            if ('赵六' === $result->getRealName() && '15800158000' === $result->getMobile()) {
                $foundEntity2 = true;
            }
        }
        $this->assertTrue($foundEntity1);
        $this->assertTrue($foundEntity2);
    }

    public function testFindBy(): void
    {
        $entity = new Consignee();
        $entity->setRealName('钱七');
        $entity->setMobile('18900189000');
        $entity->setAddress('杭州市西湖区测试路101号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['realName' => '钱七']);
        $this->assertCount(1, $results);
        $this->assertEquals('钱七', $results[0]->getRealName());
        $this->assertEquals('18900189000', $results[0]->getMobile());
    }

    public function testFindOneBy(): void
    {
        $entity = new Consignee();
        $entity->setRealName('孙八');
        $entity->setMobile('17700177000');
        $entity->setAddress('成都市锦江区测试街202号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $result = $this->repository->findOneBy(['realName' => '孙八', 'mobile' => '17700177000']);
        $this->assertInstanceOf(Consignee::class, $result);
        $this->assertEquals('孙八', $result->getRealName());
        $this->assertEquals('17700177000', $result->getMobile());
        $this->assertEquals('成都市锦江区测试街202号', $result->getAddress());
    }

    public function testCount(): void
    {
        $initialCount = $this->repository->count([]);

        $entity = new Consignee();
        $entity->setRealName('周九');
        $entity->setMobile('16600166000');
        $entity->setAddress('重庆市渝北区测试大街303号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $newCount = $this->repository->count([]);
        $this->assertEquals($initialCount + 1, $newCount);
    }

    public function testFindByMobile(): void
    {
        $entity = new Consignee();
        $entity->setRealName('吴十');
        $entity->setMobile('15500155000');
        $entity->setAddress('西安市雁塔区测试路404号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $results = $this->repository->findBy(['mobile' => '15500155000']);
        $this->assertCount(1, $results);
        $this->assertEquals('吴十', $results[0]->getRealName());
        $this->assertEquals('15500155000', $results[0]->getMobile());
    }

    public function testFindByOrderBy(): void
    {
        $entity1 = new Consignee();
        $entity1->setRealName('张A');
        $entity1->setMobile('13300133000');
        $entity1->setAddress('苏州市工业园区测试路606号');
        $entity1->setChance($this->chance);

        // 创建另一个 Chance
        $activity2 = new Activity();
        $activity2->setTitle('Test Activity3 ' . uniqid());
        $activity2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $activity2->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($activity2);

        $chance2 = new Chance();
        $chance2->setTitle('Test Chance3 ' . uniqid());
        $chance2->setActivity($activity2);
        $chance2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $chance2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($chance2);
        self::getEntityManager()->flush();

        $entity2 = new Consignee();
        $entity2->setRealName('李B');
        $entity2->setMobile('18800188000');
        $entity2->setAddress('无锡市滨湖区测试大道707号');
        $entity2->setChance($chance2);

        $this->repository->save($entity1, true);
        $this->repository->save($entity2, true);

        $results = $this->repository->findBy([], ['realName' => 'ASC']);
        $this->assertGreaterThanOrEqual(2, count($results));

        /** @var string[] $names */
        $names = array_map(fn ($consignee) => $consignee->getRealName(), $results);
        $sortedNames = $names;
        sort($sortedNames);
        $this->assertEquals($sortedNames, $names);
    }

    public function testFindByLimit(): void
    {
        // 创建多个 Chance 以便创建多个 Consignee
        $activities = [];
        $chances = [];
        for ($i = 0; $i < 5; ++$i) {
            $activity = new Activity();
            $activity->setTitle('Limit Activity ' . $i . ' ' . uniqid());
            $activity->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
            $activity->setEndTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
            self::getEntityManager()->persist($activity);
            $activities[] = $activity;

            $chance = new Chance();
            $chance->setTitle('Limit Chance ' . $i . ' ' . uniqid());
            $chance->setActivity($activity);
            $chance->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
            $chance->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
            self::getEntityManager()->persist($chance);
            $chances[] = $chance;
        }
        self::getEntityManager()->flush();

        for ($i = 0; $i < 5; ++$i) {
            $entity = new Consignee();
            $entity->setRealName('Limit Test ' . $i);
            $entity->setMobile('1990019900' . $i);
            $entity->setAddress('Limit Address ' . $i);
            $entity->setChance($chances[$i]);
            $this->repository->save($entity, false);
        }
        self::getEntityManager()->flush();

        $results = $this->repository->findBy([], null, 3);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testOneToOneRelationshipWithChance(): void
    {
        $entity = new Consignee();
        $entity->setRealName('关系测试');
        $entity->setMobile('12300123000');
        $entity->setAddress('关系测试地址808号');
        $entity->setChance($this->chance);

        $this->repository->save($entity, true);

        $this->assertInstanceOf(Chance::class, $entity->getChance());
        $this->assertEquals($this->chance->getId(), $entity->getChance()->getId());

        // 测试反向关系
        $foundChance = $entity->getChance();
        $this->assertInstanceOf(Chance::class, $foundChance);
        $this->assertEquals($this->chance->getTitle(), $foundChance->getTitle());
    }

    public function testFindOneByOrderByLogic(): void
    {
        // 创建第二个 Chance 对象用于测试
        $chance2 = new Chance();
        $chance2->setTitle('Test Chance 2 ' . uniqid());
        $chance2->setActivity($this->chance->getActivity());
        $chance2->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $chance2->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($chance2);

        // 创建多个实体以测试排序
        $entity1 = new Consignee();
        $entity1->setRealName('Z Should Be Last');
        $entity1->setMobile('14700147001');
        $entity1->setAddress('Last Address');
        $entity1->setChance($this->chance);

        $entity2 = new Consignee();
        $entity2->setRealName('A Should Be First');
        $entity2->setMobile('14700147002');
        $entity2->setAddress('First Address');
        $entity2->setChance($chance2);

        $this->repository->save($entity1, false);
        $this->repository->save($entity2, false);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([], ['realName' => 'ASC']);
        $this->assertInstanceOf(Consignee::class, $result);
        $realName = $result->getRealName();
        $this->assertIsString($realName);
        $this->assertStringContainsString('A Should Be First', $realName);
    }

    protected function createNewEntity(): object
    {
        // 为每个新实体创建一个新的 Chance
        $newChance = new Chance();
        $newChance->setTitle('Test Chance ' . uniqid());
        $newChance->setActivity($this->chance->getActivity());
        $newChance->setStartTime(new \DateTimeImmutable('2024-01-01 00:00:00'));
        $newChance->setExpireTime(new \DateTimeImmutable('2024-12-31 23:59:59'));
        self::getEntityManager()->persist($newChance);

        $entity = new Consignee();
        $entity->setRealName('Test Consignee ' . uniqid());
        $entity->setMobile('13800138' . rand(100, 999));
        $entity->setAddress('Test Address');
        $entity->setChance($newChance);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<Consignee>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(ConsigneeRepository::class);
    }
}
