<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\PoolRepository;
use PHPUnit\Framework\TestCase;

class PoolRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(PoolRepository::class));
    }
}
