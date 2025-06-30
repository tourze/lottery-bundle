<?php

namespace LotteryBundle\Tests\Integration\EventSubscriber;

use LotteryBundle\EventSubscriber\LotterySubscriber;
use PHPUnit\Framework\TestCase;

class LotterySubscriberTest extends TestCase
{
    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $subscriber = new LotterySubscriber();
        
        $this->assertInstanceOf(LotterySubscriber::class, $subscriber);
    }
}