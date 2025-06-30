<?php

namespace LotteryBundle\Tests\Integration\Repository;

use LotteryBundle\Repository\ActivityRepository;
use PHPUnit\Framework\TestCase;

class ActivityRepositoryTest extends TestCase
{
    public function test_placeholder(): void
    {
        $this->assertTrue(class_exists(ActivityRepository::class));
    }
}
