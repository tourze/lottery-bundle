<?php

namespace LotteryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\EcolBundle\EcolBundle;
use Tourze\JsonRPCHttpEndpointBundle\JsonRPCHttpEndpointBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\ResourceManageBundle\ResourceManageBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\Symfony\CronJob\CronJobBundle;
use Tourze\TextManageBundle\TextManageBundle;

class LotteryBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            JsonRPCLockBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            DoctrineIpBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
            CronJobBundle::class => ['all' => true],
            TextManageBundle::class => ['all' => true],
            ResourceManageBundle::class => ['all' => true],
            EcolBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            JsonRPCHttpEndpointBundle::class => ['all' => true],
        ];
    }
}
