<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 库存数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class StockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createStockForPrize($manager, PrizeFixtures::PRIZE_REFERENCE_1, 'PRIZE1-', 50);
        $this->createStockForPrize($manager, PrizeFixtures::PRIZE_REFERENCE_2, 'PRIZE2-', 100);
        $this->createStockForPrize($manager, PrizeFixtures::PRIZE_REFERENCE_3, 'PRIZE3-', 200);

        $manager->flush();
    }

    private function createStockForPrize(ObjectManager $manager, string $prizeRef, string $prefix, int $count): void
    {
        $prize = $this->getReference($prizeRef, Prize::class);

        for ($i = 1; $i <= $count; ++$i) {
            $stock = new Stock();
            $stock->setSn($prefix . str_pad((string) $i, 5, '0', STR_PAD_LEFT));
            $stock->setPrize($prize);
            $manager->persist($stock);

            // 每100条记录刷新一次
            if (0 === $i % 100) {
                $manager->flush();
                $manager->clear();
                $prize = $this->getReference($prizeRef, Prize::class);
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            PrizeFixtures::class,
        ];
    }
}
