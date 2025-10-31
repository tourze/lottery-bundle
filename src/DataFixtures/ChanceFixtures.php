<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 抽奖机会数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class ChanceFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const CHANCE_REFERENCE_1 = 'chance-1';
    public const CHANCE_REFERENCE_2 = 'chance-2';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('zh_CN');

        $this->createInitialChances($manager);
        $this->createBulkChances($manager, $faker);
    }

    private function createInitialChances(ObjectManager $manager): void
    {
        $chance1 = $this->createChance(
            '测试抽奖机会1',
            new \DateTimeImmutable('now'),
            new \DateTimeImmutable('+7 days'),
            ChanceStatusEnum::INIT
        );
        $chance1->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));
        $manager->persist($chance1);

        $chance2 = $this->createChance(
            '测试抽奖机会2',
            new \DateTimeImmutable('now - 1 day'),
            new \DateTimeImmutable('+6 days'),
            ChanceStatusEnum::WINNING
        );
        $chance2->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));
        $chance2->setPrize($this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class));
        $chance2->setUseTime(new \DateTimeImmutable('now'));
        $chance2->setPoolContext(['pool_id' => 2, 'pool_name' => '测试奖池2']);
        $chance2->setProbabilityContext([['original_probability' => 30, 'adjusted_probability' => 35]]);
        $manager->persist($chance2);

        $this->addReference(self::CHANCE_REFERENCE_1, $chance1);
        $this->addReference(self::CHANCE_REFERENCE_2, $chance2);
    }

    private function createChance(
        string $title,
        \DateTimeImmutable $startTime,
        \DateTimeImmutable $expireTime,
        ChanceStatusEnum $status,
    ): Chance {
        $chance = new Chance();
        $chance->setTitle($title);
        $chance->setStartTime($startTime);
        $chance->setExpireTime($expireTime);
        $chance->setValid(true);
        $chance->setStatus($status);

        return $chance;
    }

    private function createBulkChances(ObjectManager $manager, Generator $faker): void
    {
        $collections = $this->getCollections();
        $batchSize = 100;

        for ($i = 0; $i < 1000; ++$i) {
            $chance = $this->createRandomChance($faker, $collections, $i);
            $manager->persist($chance);

            if (($i % $batchSize) === 0) {
                $manager->flush();
                $manager->clear();
                $collections = $this->getCollections(); // Refresh references
            }
        }

        $manager->flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function getCollections(): array
    {
        return [
            'activities' => [
                $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class),
                $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class),
            ],
            'pools' => [
                $this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class),
                $this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class),
            ],
            'prizes' => [
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class),
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class),
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class),
                $this->getReference(PrizeFixtures::PRIZE_REFERENCE_4, Prize::class),
                null,
            ],
            'status_values' => [
                ChanceStatusEnum::INIT,
                ChanceStatusEnum::WINNING,
                ChanceStatusEnum::SENT,
                ChanceStatusEnum::EXPIRED,
            ],
            'titles' => [
                '每日登录抽奖机会', '分享活动抽奖机会', '邀请好友抽奖机会', '购物返利抽奖机会',
                '签到奖励抽奖机会', '新手礼包抽奖机会', '会员专属抽奖机会', '节日活动抽奖机会',
                '幸运大转盘抽奖机会', '限时活动抽奖机会',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $collections
     */
    private function createRandomChance(Generator $faker, array $collections, int $index): Chance
    {
        $statusValues = $collections['status_values'];
        assert(is_array($statusValues));
        $status = $faker->randomElement($statusValues);
        assert($status instanceof ChanceStatusEnum);

        $titles = $collections['titles'];
        assert(is_array($titles));
        $title = $faker->randomElement($titles);
        assert(is_string($title));

        $startDateTime = $faker->dateTimeBetween('-30 days', '+30 days');
        $startTime = \DateTimeImmutable::createFromMutable($startDateTime);
        $expireTime = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween($startDateTime, '+60 days'));

        $chance = $this->createChance(
            $title . '-' . $index,
            $startTime,
            $expireTime,
            $status
        );

        $this->configureChanceByStatus($chance, $status, $faker, $collections);

        $activities = $collections['activities'];
        assert(is_array($activities));
        $activity = $faker->randomElement($activities);
        assert($activity instanceof Activity);
        $chance->setActivity($activity);

        $chance->setValid($faker->boolean(90));
        $this->addOptionalRemark($chance, $faker, $index);

        return $chance;
    }

    /**
     * @param array<string, mixed> $collections
     */
    private function configureChanceByStatus(Chance $chance, ChanceStatusEnum $status, Generator $faker, array $collections): void
    {
        if (ChanceStatusEnum::WINNING === $status || ChanceStatusEnum::SENT === $status) {
            $this->configureUsedChance($chance, $status, $faker, $collections);
        } elseif (ChanceStatusEnum::INIT === $status) {
            $this->configureInitChance($chance, $faker, $collections);
        }
    }

    /**
     * @param array<string, mixed> $collections
     */
    private function configureUsedChance(Chance $chance, ChanceStatusEnum $status, Generator $faker, array $collections): void
    {
        $useDateTime = $faker->dateTimeBetween('-30 days', 'now');
        $useTime = \DateTimeImmutable::createFromMutable($useDateTime);
        $chance->setUseTime($useTime);

        $prizes = $collections['prizes'];
        assert(is_array($prizes));
        $prizeIndex = $faker->numberBetween(0, count($prizes) - 2);
        $prize = $prizes[$prizeIndex];
        assert($prize instanceof Prize || null === $prize);
        $chance->setPrize($prize);

        $pools = $collections['pools'];
        assert(is_array($pools));
        $pool = $faker->randomElement($pools);
        assert($pool instanceof Pool);
        $chance->setPool($pool);
        $chance->setPoolContext(['pool_id' => $pool->getId(), 'pool_name' => $pool->getTitle()]);
        $chance->setProbabilityContext([[
            'original_probability' => $faker->numberBetween(5, 50),
            'adjusted_probability' => $faker->numberBetween(5, 50),
        ]]);

        if (ChanceStatusEnum::SENT === $status) {
            $chance->setSendTime(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween($useDateTime, 'now')));
            $chance->setSendResult(['status' => 'success', 'time' => $faker->unixTime()]);
        }
    }

    /**
     * @param array<string, mixed> $collections
     */
    private function configureInitChance(Chance $chance, Generator $faker, array $collections): void
    {
        if ($faker->boolean(25)) {
            $pools = $collections['pools'];
            assert(is_array($pools));
            $pool = $faker->randomElement($pools);
            assert($pool instanceof Pool);
            $chance->setPool($pool);
        }

        if ($faker->boolean(20)) {
            $prizes = $collections['prizes'];
            assert(is_array($prizes));
            $prizeIndex = $faker->numberBetween(0, count($prizes) - 2);
            $prize = $prizes[$prizeIndex];
            assert($prize instanceof Prize || null === $prize);
            $chance->setPrize($prize);
        }
    }

    private function addOptionalRemark(Chance $chance, Generator $faker, int $index): void
    {
        if ($faker->boolean(25)) {
            $remarks = [
                '系统自动生成', '推广活动获得', '管理员手动添加', '活动奖励', '限时福利',
                '邀请好友奖励', '社区活动奖励', '特殊节日福利', '会员专属权益', '忠诚度奖励',
            ];
            $remark = $faker->randomElement($remarks);
            assert(is_string($remark));
            $chance->setRemark($remark . '-' . $index);
        }
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixtures::class,
            PrizeFixtures::class,
            PoolFixtures::class,
        ];
    }
}
