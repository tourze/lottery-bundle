<?php

namespace LotteryBundle\Tests\Unit\Procedure;

use LotteryBundle\Procedure\SaveOrUpdateLotteryConsignee;
use PHPUnit\Framework\TestCase;

class SaveOrUpdateLotteryConsigneeTest extends TestCase
{
    public function test_className_exists(): void
    {
        $this->assertTrue(class_exists(SaveOrUpdateLotteryConsignee::class));
    }
}