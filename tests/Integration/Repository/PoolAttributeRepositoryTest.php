<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\PoolAttributeRepository;
use PHPUnit\Framework\TestCase;

class PoolAttributeRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(PoolAttributeRepository::class));
    }
}
