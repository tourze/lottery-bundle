<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\ChanceRepository;
use PHPUnit\Framework\TestCase;

class ChanceRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(ChanceRepository::class));
    }
}
