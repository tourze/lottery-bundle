<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;

/**
 * 抽奖机会数据填充
 */
class ChanceFixtures extends Fixture implements DependentFixtureInterface
{
    // 使用常量定义引用名称
    public const CHANCE_REFERENCE_1 = 'chance-1';
    public const CHANCE_REFERENCE_2 = 'chance-2';

    public function load(ObjectManager $manager): void
    {
        // 创建Faker实例
        $faker = Factory::create('zh_CN');

        // 保留原有的两个特殊引用对象
        $chance1 = new Chance();
        $chance1->setTitle('测试抽奖机会1');
        $chance1->setStartTime(new \DateTime('now'));
        $chance1->setExpireTime(new \DateTime('+7 days'));
        $chance1->setValid(true);
        $chance1->setStatus(ChanceStatusEnum::INIT);
        $chance1->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));

        $manager->persist($chance1);

        $chance2 = new Chance();
        $chance2->setTitle('测试抽奖机会2');
        $chance2->setStartTime(new \DateTime('now - 1 day'));
        $chance2->setExpireTime(new \DateTime('+6 days'));
        $chance2->setUseTime(new \DateTime('now'));
        $chance2->setValid(true);
        $chance2->setStatus(ChanceStatusEnum::WINNING);
        $chance2->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));
        $chance2->setPrize($this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class));

        $chance2->setPoolContext([
            'pool_id' => 2,
            'pool_name' => '测试奖池2',
        ]);

        $chance2->setProbabilityContext([
            'original_probability' => 30,
            'adjusted_probability' => 35,
        ]);

        $manager->persist($chance2);

        // 添加引用以便其他 Fixture 使用
        $this->addReference(self::CHANCE_REFERENCE_1, $chance1);
        $this->addReference(self::CHANCE_REFERENCE_2, $chance2);

        // 批量生成至少1000个抽奖机会
        $activities = [
            $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class),
            $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class)
        ];

        $pools = [
            $this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class),
            $this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class)
        ];

        $prizes = [
            $this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class),
            $this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class),
            $this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class),
            $this->getReference(PrizeFixtures::PRIZE_REFERENCE_4, Prize::class),
            null // 部分抽奖机会未指定奖品
        ];

        $statusValues = [
            ChanceStatusEnum::INIT,
            ChanceStatusEnum::WINNING,
            ChanceStatusEnum::SENT,
            ChanceStatusEnum::EXPIRED
        ];

        $batchSize = 100; // 批处理大小，用于提高性能

        for ($i = 0; $i < 1000; $i++) {
            $chance = new Chance();

            // 使用Faker生成随机标题
            $titles = ['每日登录抽奖机会', '分享活动抽奖机会', '邀请好友抽奖机会', '购物返利抽奖机会',
                '签到奖励抽奖机会', '新手礼包抽奖机会', '会员专属抽奖机会', '节日活动抽奖机会',
                '幸运大转盘抽奖机会', '限时活动抽奖机会'];
            $chance->setTitle($faker->randomElement($titles) . '-' . $i);

            // 使用Faker生成随机时间
            $startTime = $faker->dateTimeBetween('-30 days', '+30 days');
            $chance->setStartTime($startTime);
            $chance->setExpireTime($faker->dateTimeBetween($startTime, '+60 days'));

            // 随机状态
            $status = $faker->randomElement($statusValues);
            $chance->setStatus($status);

            // 如果是已中奖或已发送状态，设置使用时间和奖品
            if ($status === ChanceStatusEnum::WINNING || $status === ChanceStatusEnum::SENT) {
                $useTime = $faker->dateTimeBetween('-30 days', 'now');
                $chance->setUseTime($useTime);

                // 已使用的设置奖品，避免null
                $prizeIndex = $faker->numberBetween(0, count($prizes) - 2); // 排除最后一个null项
                $prize = $prizes[$prizeIndex];
                $chance->setPrize($prize);

                // 添加上下文信息
                $poolIndex = $faker->numberBetween(0, count($pools) - 1);
                $pool = $pools[$poolIndex];
                $chance->setPool($pool);

                $chance->setPoolContext([
                    'pool_id' => $pool->getId(),
                    'pool_name' => $pool->getTitle(),
                ]);

                $chance->setProbabilityContext([
                    'original_probability' => $faker->numberBetween(5, 50),
                    'adjusted_probability' => $faker->numberBetween(5, 50),
                ]);

                // 如果状态是SENT，设置发送时间
                if ($status === ChanceStatusEnum::SENT) {
                    $chance->setSendTime($faker->dateTimeBetween($useTime, 'now'));
                    $chance->setSendResult(['status' => 'success', 'time' => $faker->unixTime()]);
                }
            } elseif ($status === ChanceStatusEnum::INIT) {
                // 未使用的可能有指定的奖池或奖品
                if ($faker->boolean(25)) { // 25%的概率指定奖池
                    $poolIndex = $faker->numberBetween(0, count($pools) - 1);
                    $chance->setPool($pools[$poolIndex]);
                }

                if ($faker->boolean(20)) { // 20%的概率指定奖品
                    $prizeIndex = $faker->numberBetween(0, count($prizes) - 2); // 排除null
                    $chance->setPrize($prizes[$prizeIndex]);
                }
            }

            // 活动关联
            $activityIndex = $faker->numberBetween(0, count($activities) - 1);
            $chance->setActivity($activities[$activityIndex]);

            // 随机有效性
            $chance->setValid($faker->boolean(90)); // 90%的概率有效

            // 随机备注
            if ($faker->boolean(25)) { // 25%的概率有备注
                $remarks = ['系统自动生成', '推广活动获得', '管理员手动添加', '活动奖励', '限时福利',
                    '邀请好友奖励', '社区活动奖励', '特殊节日福利', '会员专属权益', '忠诚度奖励'];
                $chance->setRemark($faker->randomElement($remarks) . '-' . $i);
            }

            $manager->persist($chance);

            // 每处理100条数据就刷新一次，避免内存问题
            if (($i % $batchSize) === 0) {
                $manager->flush();
                $manager->clear(); // 清理实体管理器

                // 重新取得引用
                $activities = [
                    $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class),
                    $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class)
                ];

                $pools = [
                    $this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class),
                    $this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class)
                ];

                $prizes = [
                    $this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class),
                    $this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class),
                    $this->getReference(PrizeFixtures::PRIZE_REFERENCE_3, Prize::class),
                    $this->getReference(PrizeFixtures::PRIZE_REFERENCE_4, Prize::class),
                    null
                ];
            }
        }

        // 最后一次刷新
        $manager->flush();
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
