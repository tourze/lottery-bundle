<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Pool;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 奖池数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class PoolFixtures extends Fixture implements FixtureGroupInterface
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

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }
}
