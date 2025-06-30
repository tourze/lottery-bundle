<?php

namespace LotteryBundle\Tests\Unit\ExpressionLanguage\Function;

use LotteryBundle\ExpressionLanguage\Function\ChanceFunctionProvider;
use PHPUnit\Framework\TestCase;

class ChanceFunctionProviderTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(ChanceFunctionProvider::class));
    }
}