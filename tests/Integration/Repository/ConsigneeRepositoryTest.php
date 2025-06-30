<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\ConsigneeRepository;
use PHPUnit\Framework\TestCase;

class ConsigneeRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(ConsigneeRepository::class));
    }
}
