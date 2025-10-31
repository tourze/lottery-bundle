<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 奖品数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class PrizeFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const PRIZE_REFERENCE_1 = 'prize-1';
    public const PRIZE_REFERENCE_2 = 'prize-2';
    public const PRIZE_REFERENCE_3 = 'prize-3';
    public const PRIZE_REFERENCE_4 = 'prize-4';

    public function load(ObjectManager $manager): void
    {
        // 创建第一个奖品
        $prize1 = new Prize();
        $prize1->setName('一等奖');
        $prize1->setContent('价值1000元的礼品卡');
        $prize1->setType('physical');
        $prize1->setQuantity(10);
        $prize1->setValue('1000.00');
        $prize1->setProbability(10);
        $prize1->setPicture(
            'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=400&h=300&fit=crop&crop=center'
        );
        $prize1->setNeedConsignee(true);
        $prize1->setValid(true);
        $prize1->setSortNumber(100);
        $prize1->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class));

        $manager->persist($prize1);

        // 创建第二个奖品
        $prize2 = new Prize();
        $prize2->setName('二等奖');
        $prize2->setContent('价值500元的礼品');
        $prize2->setType('physical');
        $prize2->setQuantity(20);
        $prize2->setValue('500.00');
        $prize2->setProbability(20);
        $prize2->setPicture(
            'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400&h=300&fit=crop&crop=center'
        );
        $prize2->setNeedConsignee(true);
        $prize2->setValid(true);
        $prize2->setSortNumber(90);
        $prize2->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class));

        $manager->persist($prize2);

        // 创建第三个奖品
        $prize3 = new Prize();
        $prize3->setName('三等奖');
        $prize3->setContent('价值100元的优惠券');
        $prize3->setType('coupon');
        $prize3->setTypeId('COUPON123');
        $prize3->setQuantity(50);
        $prize3->setValue('100.00');
        $prize3->setProbability(30);
        $prize3->setPicture(
            'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=400&h=300&fit=crop&crop=center'
        );
        $prize3->setNeedConsignee(false);
        $prize3->setValid(true);
        $prize3->setSortNumber(80);
        $prize3->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class));

        $manager->persist($prize3);

        // 创建兜底奖品
        $prize4 = new Prize();
        $prize4->setName('安慰奖');
        $prize4->setContent('谢谢参与');
        $prize4->setType('virtual');
        $prize4->setQuantity(1000);
        $prize4->setValue('0.00');
        $prize4->setProbability(40);
        $prize4->setPicture(
            'https://images.unsplash.com/photo-1472214103451-9374bd1c798e?w=400&h=300&fit=crop&crop=center'
        );
        $prize4->setNeedConsignee(false);
        $prize4->setIsDefault(true);
        $prize4->setValid(true);
        $prize4->setSortNumber(70);
        $prize4->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class));

        $manager->persist($prize4);

        $manager->flush();

        // 添加引用以便其他 Fixture 使用
        $this->addReference(self::PRIZE_REFERENCE_1, $prize1);
        $this->addReference(self::PRIZE_REFERENCE_2, $prize2);
        $this->addReference(self::PRIZE_REFERENCE_3, $prize3);
        $this->addReference(self::PRIZE_REFERENCE_4, $prize4);
    }

    public function getDependencies(): array
    {
        return [
            PoolFixtures::class,
        ];
    }
}
