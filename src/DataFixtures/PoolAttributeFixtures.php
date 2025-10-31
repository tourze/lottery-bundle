<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * 奖池属性数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class PoolAttributeFixtures extends Fixture implements DependentFixtureInterface
{
    // 预设属性键值对
    private const ATTRIBUTE_CONFIGS = [
        // 界面配置
        'theme' => ['节日', '日常', '促销', '会员', '新品', '限时', '特惠', '季节', '节气'],
        'color' => ['#FF5733', '#3498DB', '#2ECC71', '#9B59B6', '#F1C40F', '#1ABC9C', '#E74C3C', '#34495E'],
        'style' => ['modern', 'classic', 'elegant', 'fun', 'simple', 'luxury', 'cute', 'techno'],
        'display_mode' => ['grid', 'list', 'carousel', 'wheel', 'cards', 'slots'],

        // 抽奖配置
        'draw_type' => ['random', 'weighted', 'sequential', 'timed', 'limited', 'progressive'],
        'max_draw' => ['1', '3', '5', '10', '20', '50', '100'],
        'draw_interval' => ['0', '1', '5', '10', '30', '60', '1440'],
        'reset_time' => ['daily', 'weekly', 'monthly', 'never', 'custom'],

        // 高级配置
        'encryption' => ['none', 'basic', 'advanced', 'quantum'],
        'logging' => ['none', 'basic', 'detailed', 'full'],
        'notification' => ['none', 'email', 'sms', 'push', 'all'],
        'anti_cheat' => ['none', 'basic', 'advanced', 'ai'],
        'algorithm' => ['standard', 'fair', 'progressive', 'loyalty', 'vip'],
    ];

    public function load(ObjectManager $manager): void
    {
        // 保留原有的4个属性
        // 为奖池1添加属性
        $poolAttr1 = new PoolAttribute();
        $poolAttr1->setName('theme');
        $poolAttr1->setValue('节日');
        $poolAttr1->setRemark('奖池主题');
        $poolAttr1->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class));

        $manager->persist($poolAttr1);

        $poolAttr2 = new PoolAttribute();
        $poolAttr2->setName('color');
        $poolAttr2->setValue('#FF5733');
        $poolAttr2->setRemark('奖池主题色');
        $poolAttr2->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class));

        $manager->persist($poolAttr2);

        // 为奖池2添加属性
        $poolAttr3 = new PoolAttribute();
        $poolAttr3->setName('theme');
        $poolAttr3->setValue('日常');
        $poolAttr3->setRemark('奖池主题');
        $poolAttr3->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class));

        $manager->persist($poolAttr3);

        $poolAttr4 = new PoolAttribute();
        $poolAttr4->setName('color');
        $poolAttr4->setValue('#3498DB');
        $poolAttr4->setRemark('奖池主题色');
        $poolAttr4->setPool($this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class));

        $manager->persist($poolAttr4);

        // 获取奖池引用数组
        $pools = [
            $this->getReference(PoolFixtures::POOL_REFERENCE_1, Pool::class),
            $this->getReference(PoolFixtures::POOL_REFERENCE_2, Pool::class),
        ];

        // 为每个奖池添加更多属性
        foreach ($pools as $pool) {
            $addedAttributes = []; // 用于记录已添加的属性，避免重复

            // 已添加了theme和color
            $addedAttributes['theme'] = true;
            $addedAttributes['color'] = true;

            // 为每个奖池添加剩余所有的属性
            foreach (self::ATTRIBUTE_CONFIGS as $name => $values) {
                // 跳过已添加的属性
                if (isset($addedAttributes[$name])) {
                    continue;
                }

                // 随机选择一个值
                $valueIndex = rand(0, count($values) - 1);
                $value = $values[$valueIndex];

                $attribute = new PoolAttribute();
                $attribute->setName($name);
                $attribute->setValue($value);
                $attribute->setRemark($this->generateRemark($name));
                $attribute->setPool($pool);

                $manager->persist($attribute);

                $addedAttributes[$name] = true;
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
            'theme' => '奖池主题',
            'color' => '奖池主题色',
            'style' => '奖池风格',
            'display_mode' => '展示模式',
            'draw_type' => '抽奖类型',
            'max_draw' => '最大抽奖次数',
            'draw_interval' => '抽奖间隔（分钟）',
            'reset_time' => '重置时间',
            'encryption' => '加密方式',
            'logging' => '日志级别',
            'notification' => '通知方式',
            'anti_cheat' => '防作弊级别',
            'algorithm' => '抽奖算法',
        ];

        return $remarks[$name] ?? '配置参数';
    }

    public function getDependencies(): array
    {
        return [
            PoolFixtures::class,
        ];
    }
}
