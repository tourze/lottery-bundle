<?php

namespace LotteryBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class LotteryExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
