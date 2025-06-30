<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\PrizeRepository;
use PHPUnit\Framework\TestCase;

class PrizeRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(PrizeRepository::class));
    }
}
