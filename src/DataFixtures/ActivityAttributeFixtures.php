<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\ActivityAttribute;

/**
 * 活动属性数据填充
 */
class ActivityAttributeFixtures extends Fixture implements DependentFixtureInterface
{
    // 预设属性键值对
    private const ATTRIBUTE_CONFIGS = [
        // 界面配置
        'background_color' => ['#FFEBCD', '#E0FFFF', '#F0FFF0', '#FFF0F5', '#F5F5DC'],
        'text_color' => ['#000000', '#333333', '#666666', '#0066CC', '#FF6600'],
        'button_color' => ['#FF5733', '#3498DB', '#2ECC71', '#9B59B6', '#F1C40F'],
        'border_radius' => ['4px', '6px', '8px', '10px', '12px'],
        'animation_type' => ['fade', 'slide', 'bounce', 'flip', 'rotate'],

        // 功能配置
        'max_chances' => ['3', '5', '10', '20', '30'],
        'daily_chances' => ['1', '2', '3', '5', '10'],
        'show_winners' => ['true', 'false'],
        'need_login' => ['true', 'false'],
        'share_bonus' => ['1', '2', '3', '0', '5'],

        // 高级配置
        'probability_adjust' => ['1.0', '1.1', '0.9', '1.2', '0.8'],
        'auto_send_prize' => ['true', 'false'],
        'prize_expire_days' => ['7', '15', '30', '45', '60'],
        'chance_expire_hours' => ['24', '48', '72', '168', '336'],
        'max_stock_per_user' => ['1', '2', '3', '5', '10']
    ];

    public function load(ObjectManager $manager): void
    {
        // 保留原始的4个属性
        // 为活动1添加属性
        $activityAttr1 = new ActivityAttribute();
        $activityAttr1->setName('background_color');
        $activityAttr1->setValue('#FFEBCD');
        $activityAttr1->setRemark('活动背景色');
        $activityAttr1->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));

        $manager->persist($activityAttr1);

        $activityAttr2 = new ActivityAttribute();
        $activityAttr2->setName('max_chances');
        $activityAttr2->setValue('3');
        $activityAttr2->setRemark('每人最大抽奖次数');
        $activityAttr2->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class));

        $manager->persist($activityAttr2);

        // 为活动2添加属性
        $activityAttr3 = new ActivityAttribute();
        $activityAttr3->setName('background_color');
        $activityAttr3->setValue('#E0FFFF');
        $activityAttr3->setRemark('活动背景色');
        $activityAttr3->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class));

        $manager->persist($activityAttr3);

        $activityAttr4 = new ActivityAttribute();
        $activityAttr4->setName('max_chances');
        $activityAttr4->setValue('5');
        $activityAttr4->setRemark('每人最大抽奖次数');
        $activityAttr4->setActivity($this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class));

        $manager->persist($activityAttr4);

        // 获取活动引用数组
        $activities = [
            $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_1, Activity::class),
            $this->getReference(ActivityFixtures::ACTIVITY_REFERENCE_2, Activity::class)
        ];

        // 为每个活动添加更多属性
        foreach ($activities as $activity) {
            $addedAttributes = []; // 用于记录已添加的属性，避免重复

            if ($activity === $activities[0]) {
                // 为活动1已添加了background_color和max_chances
                $addedAttributes['background_color'] = true;
                $addedAttributes['max_chances'] = true;
            } else {
                // 为活动2已添加了background_color和max_chances
                $addedAttributes['background_color'] = true;
                $addedAttributes['max_chances'] = true;
            }

            // 为每个活动最多添加10个额外属性
            foreach (self::ATTRIBUTE_CONFIGS as $name => $values) {
                // 跳过已添加的属性
                if (isset($addedAttributes[$name])) {
                    continue;
                }

                // 随机选择一个值
                $valueIndex = rand(0, count($values) - 1);
                $value = $values[$valueIndex];

                $attribute = new ActivityAttribute();
                $attribute->setName($name);
                $attribute->setValue($value);
                $attribute->setRemark($this->generateRemark($name));
                $attribute->setActivity($activity);

                $manager->persist($attribute);

                $addedAttributes[$name] = true;

                // 到达10个属性后停止
                if (count($addedAttributes) >= 10) {
                    break;
                }
            }
        }

        $manager->flush();
    }

    /**
     * 根据属性名生成备注说明
     */
    private function generateRemark(string $name): string
    {
        $remarks = [
            'background_color' => '活动背景色',
            'text_color' => '文本颜色',
            'button_color' => '按钮颜色',
            'border_radius' => '边框圆角',
            'animation_type' => '动画类型',
            'max_chances' => '每人最大抽奖次数',
            'daily_chances' => '每日最大抽奖次数',
            'show_winners' => '是否显示中奖名单',
            'need_login' => '是否需要登录',
            'share_bonus' => '分享获得抽奖次数',
            'probability_adjust' => '概率调整系数',
            'auto_send_prize' => '是否自动发奖',
            'prize_expire_days' => '奖品过期天数',
            'chance_expire_hours' => '抽奖机会过期小时数',
            'max_stock_per_user' => '每用户最大库存数量'
        ];

        return $remarks[$name] ?? '配置参数';
    }

    public function getDependencies(): array
    {
        return [
            ActivityFixtures::class,
        ];
    }
}
