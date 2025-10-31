<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Enum\ChanceStatusEnum;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 收货人数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class ConsigneeFixtures extends Fixture implements DependentFixtureInterface
{
    // 省份列表
    private const PROVINCES = [
        '北京市', '上海市', '天津市', '重庆市',
        '河北省', '山西省', '辽宁省', '吉林省', '黑龙江省',
        '江苏省', '浙江省', '安徽省', '福建省', '江西省', '山东省',
        '河南省', '湖北省', '湖南省', '广东省', '海南省',
        '四川省', '贵州省', '云南省', '陕西省', '甘肃省', '青海省',
    ];

    // 城市列表
    private const CITIES = [
        '北京', '上海', '广州', '深圳', '杭州',
        '南京', '武汉', '成都', '重庆', '西安',
        '天津', '苏州', '郑州', '长沙', '沈阳',
        '青岛', '宁波', '东莞', '无锡', '厦门',
    ];

    // 区县列表
    private const DISTRICTS = [
        '朝阳区', '海淀区', '东城区', '西城区', '丰台区',
        '黄浦区', '徐汇区', '长宁区', '静安区', '普陀区',
        '天河区', '越秀区', '白云区', '荔湾区', '海珠区',
        '南山区', '福田区', '罗湖区', '龙岗区', '宝安区',
    ];

    // 街道列表
    private const STREETS = [
        '人民路', '中山路', '建设路', '解放路', '新华路',
        '和平路', '幸福路', '长江路', '黄河路', '珠江路',
        '朝阳街', '东风街', '北京路', '上海路', '南京路',
        '胜利大道', '文化大道', '科技大道', '创业大道', '富强大道',
    ];

    // 小区列表
    private const COMMUNITIES = [
        '阳光小区', '翠园小区', '和谐家园', '金色家园', '绿色家园',
        '幸福小区', '康乐小区', '富贵花园', '竹苑小区', '枫林湾',
        '龙湖花园', '碧水云天', '锦绣山河', '盛世华庭', '香榭丽舍',
        '保利花园', '万科城市花园', '恒大名都', '中海国际', '绿地世纪城',
    ];

    public function load(ObjectManager $manager): void
    {
        // 创建Faker实例
        $faker = Factory::create('zh_CN'); // 使用中文本地化

        // 为原始的chance-2创建收货人
        $consignee = new Consignee();
        $consignee->setRealName('张三');
        $consignee->setMobile('13800138000');
        $consignee->setAddress('北京市朝阳区某某街道某某小区1号楼1单元101室');
        $consignee->setChance($this->getReference(ChanceFixtures::CHANCE_REFERENCE_2, Chance::class));

        $manager->persist($consignee);

        // 获取实物奖品列表
        $physicalPrizes = [
            $this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class),
            $this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class),
        ];

        // 生成200个收货人信息，与前面生成的Chance数据模拟关联
        $batchSize = 50;

        for ($i = 0; $i < 200; ++$i) {
            $consignee = new Consignee();

            // 使用Faker生成真实姓名
            $consignee->setRealName($faker->name());

            // 使用Faker生成手机号
            $consignee->setMobile($faker->phoneNumber());

            // 生成中国风格的地址
            $province = self::PROVINCES[array_rand(self::PROVINCES)];
            $city = self::CITIES[array_rand(self::CITIES)];
            $district = self::DISTRICTS[array_rand(self::DISTRICTS)];

            // 随机决定是街道+门牌号还是小区+楼号+单元+门牌号
            if ($faker->boolean()) {
                $street = self::STREETS[array_rand(self::STREETS)];
                $address = sprintf(
                    '%s%s%s%d号',
                    $province,
                    $city,
                    $street,
                    $faker->numberBetween(1, 200)
                );
            } else {
                $community = self::COMMUNITIES[array_rand(self::COMMUNITIES)];
                $address = sprintf(
                    '%s%s%s%s%d栋%d单元%d号',
                    $province,
                    $city,
                    $district,
                    $community,
                    $faker->numberBetween(1, 30),
                    $faker->numberBetween(1, 10),
                    $faker->numberBetween(101, 2505)
                );
            }

            $consignee->setAddress($address);

            // 创建新的Chance并设置关系
            $chance = new Chance();
            $chance->setTitle('实物奖品中奖-' . $i);
            $chance->setStartTime(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 days', 'now')));
            $chance->setExpireTime(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+30 days')));
            $chance->setUseTime(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-10 days', 'now')));
            $chance->setValid(true);
            $chance->setStatus(ChanceStatusEnum::WINNING);

            // 随机分配活动
            if ($faker->boolean()) {
                $chance->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));
            } else {
                $chance->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class));
            }

            // 分配实物奖品
            $prizeIndex = $faker->numberBetween(0, count($physicalPrizes) - 1);
            $chance->setPrize($physicalPrizes[$prizeIndex]);

            // 设置Chance的收货人
            $chance->setConsignee($consignee);

            // 先持久化Chance
            $manager->persist($chance);

            // 每批处理之后刷新
            if (($i % $batchSize) === 0) {
                $manager->flush();
                $manager->clear();

                // 重新获取引用
                $physicalPrizes = [
                    $this->getReference(PrizeFixtures::PRIZE_REFERENCE_1, Prize::class),
                    $this->getReference(PrizeFixtures::PRIZE_REFERENCE_2, Prize::class),
                ];
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ChanceFixtures::class,
            PrizeFixtures::class,
            ActivityFixtures::class,
        ];
    }
}
