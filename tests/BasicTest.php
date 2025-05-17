<?php

namespace LotteryBundle\Tests;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function test_packageExists(): void
    {
        $this->assertTrue(class_exists(\LotteryBundle\LotteryBundle::class));
        $this->assertTrue(interface_exists(\Tourze\BundleDependency\BundleDependencyInterface::class));
    }
} 