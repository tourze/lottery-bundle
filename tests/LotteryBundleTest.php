<?php

namespace LotteryBundle\Tests;

use LotteryBundle\LotteryBundle;
use PHPUnit\Framework\TestCase;
use Tourze\BundleDependency\BundleDependencyInterface;

class LotteryBundleTest extends TestCase
{
    /**
     * 测试 LotteryBundle 是否实现了正确的接口
     */
    public function test_bundle_implementsCorrectInterface(): void
    {
        $bundle = new LotteryBundle();
        $this->assertInstanceOf(BundleDependencyInterface::class, $bundle);
    }

    /**
     * 测试依赖包是否正确定义
     */
    public function test_getBundleDependencies_returnsCorrectDependencies(): void
    {
        $dependencies = LotteryBundle::getBundleDependencies();
        
        $this->assertNotEmpty($dependencies);
        
        // 验证关键依赖是否存在
        $this->assertArrayHasKey(\Tourze\JsonRPCLockBundle\JsonRPCLockBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\Symfony\CronJob\CronJobBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\TextManageBundle\TextManageBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\ResourceManageBundle\ResourceManageBundle::class, $dependencies);
        $this->assertArrayHasKey(\Tourze\EcolBundle\EcolBundle::class, $dependencies);
        
        // 验证依赖配置
        foreach ($dependencies as $dependency) {
            $this->assertArrayHasKey('all', $dependency);
            $this->assertTrue($dependency['all']);
        }
    }
} 