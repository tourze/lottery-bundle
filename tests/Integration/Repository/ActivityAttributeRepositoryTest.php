<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\ActivityAttributeRepository;
use PHPUnit\Framework\TestCase;

class ActivityAttributeRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(ActivityAttributeRepository::class));
    }
}