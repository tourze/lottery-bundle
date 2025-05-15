<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Pool;

/**
 * 奖池数据填充
 */
class PoolFixtures extends Fixture
{
    // 使用常量定义引用名称
    public const POOL_REFERENCE_1 = 'pool-1';
    public const POOL_REFERENCE_2 = 'pool-2';

    public function load(ObjectManager $manager): void
    {
        // 创建第一个奖池
        $pool1 = new Pool();
        $pool1->setTitle('测试奖池1');
        $pool1->setValid(true);

        $manager->persist($pool1);

        // 创建第二个奖池
        $pool2 = new Pool();
        $pool2->setTitle('测试奖池2');
        $pool2->setValid(true);

        $manager->persist($pool2);

        $manager->flush();

        // 添加引用以便其他 Fixture 使用
        $this->addReference(self::POOL_REFERENCE_1, $pool1);
        $this->addReference(self::POOL_REFERENCE_2, $pool2);
    }
}
