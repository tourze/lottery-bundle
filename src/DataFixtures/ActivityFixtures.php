<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Pool;

/**
 * 抽奖活动数据填充
 */
class ActivityFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const ACTIVITY_REFERENCE_1 = 'activity-1';
    public const ACTIVITY_REFERENCE_2 = 'activity-2';

    public function load(ObjectManager $manager): void
    {
        // 创建第一个活动
        $activity1 = new Activity();
        $activity1->setTitle('测试活动1');
        $activity1->setTextRule('这是测试活动1的规则说明');
        $activity1->setStartTime(new \DateTime('now'));
        $activity1->setEndTime(new \DateTime('+30 days'));
        $activity1->setLastRedeemTime(new \DateTime('+45 days'));
        $activity1->setHeadPhoto('https://example.com/images/activity1.jpg');
        $activity1->setValid(true);

        // 关联奖池
        $activity1->addPool($this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class));

        $manager->persist($activity1);

        // 创建第二个活动
        $activity2 = new Activity();
        $activity2->setTitle('测试活动2');
        $activity2->setTextRule('这是测试活动2的规则说明');
        $activity2->setStartTime(new \DateTime('+5 days'));
        $activity2->setEndTime(new \DateTime('+60 days'));
        $activity2->setLastRedeemTime(new \DateTime('+75 days'));
        $activity2->setHeadPhoto('https://example.com/images/activity2.jpg');
        $activity2->setShareTitle('分享赢大奖');
        $activity2->setSharePicture('https://example.com/images/share.jpg');
        $activity2->setSharePath('/share/activity2');
        $activity2->setValid(true);

        // 关联奖池
        $activity2->addPool($this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class));

        $manager->persist($activity2);

        $manager->flush();

        // 添加引用以便其他 Fixture 使用
        $this->addReference(self::ACTIVITY_REFERENCE_1, $activity1);
        $this->addReference(self::ACTIVITY_REFERENCE_2, $activity2);
    }

    public function getDependencies(): array
    {
        return [
            PoolFixtures::class,
        ];
    }
}
