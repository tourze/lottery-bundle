<?php

namespace LotteryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class LotteryBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\JsonRPCLockBundle\JsonRPCLockBundle::class => ['all' => true],
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
            \Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class => ['all' => true],
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \Tourze\DoctrineIpBundle\DoctrineIpBundle::class => ['all' => true],
            \Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
            \Tourze\Symfony\CronJob\CronJobBundle::class => ['all' => true],
            \Tourze\TextManageBundle\TextManageBundle::class => ['all' => true],
            \Tourze\ResourceManageBundle\ResourceManageBundle::class => ['all' => true],
            \Tourze\EcolBundle\EcolBundle::class => ['all' => true],
            \Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle::class => ['all' => true],
            \Tourze\JsonRPCHttpEndpointBundle\JsonRPCHttpEndpointBundle::class => ['all' => true],
        ];
    }
}
