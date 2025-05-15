<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;

/**
 * 库存数据填充
 */
class StockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 为一等奖添加库存
        for ($i = 1; $i <= 50; $i++) {
            $stock = new Stock();
            $stock->setSn('PRIZE1-' . str_pad($i, 5, '0', STR_PAD_LEFT));
            $stock->setPrize($this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class));

            $manager->persist($stock);

            // 每100条记录刷新一次
            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();
                // 重新获取参考
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class);
            }
        }

        // 为二等奖添加库存
        for ($i = 1; $i <= 100; $i++) {
            $stock = new Stock();
            $stock->setSn('PRIZE2-' . str_pad($i, 5, '0', STR_PAD_LEFT));
            $stock->setPrize($this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class));

            $manager->persist($stock);

            // 每100条记录刷新一次
            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();
                // 重新获取参考
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class);
            }
        }

        // 为三等奖添加库存
        for ($i = 1; $i <= 200; $i++) {
            $stock = new Stock();
            $stock->setSn('PRIZE3-' . str_pad($i, 5, '0', STR_PAD_LEFT));
            $stock->setPrize($this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class));

            $manager->persist($stock);

            // 每100条记录刷新一次
            if ($i % 100 === 0) {
                $manager->flush();
                $manager->clear();
                // 重新获取参考
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class);
            }
        }

        // 最后一次刷新
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PrizeFixtures::class,
        ];
    }
}
