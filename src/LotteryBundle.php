<?php

namespace LotteryBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\EcolBundle\EcolBundle;
use Tourze\JsonRPCHttpEndpointBundle\JsonRPCHttpEndpointBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use Tourze\RaffleCoreBundle\RaffleCoreBundle;
use Tourze\ResourceManageBundle\ResourceManageBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\Symfony\CronJob\CronJobBundle;
use Tourze\TextManageBundle\TextManageBundle;

class LotteryBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
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
            JsonRPCSecurityBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            RaffleCoreBundle::class => ['all' => true],
        ];
    }
}
