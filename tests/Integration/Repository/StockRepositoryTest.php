<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\StockRepository;
use PHPUnit\Framework\TestCase;

class StockRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(StockRepository::class));
    }
}
