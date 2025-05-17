<?php

namespace LotteryBundle\Tests\Enum;

use LotteryBundle\Enum\ChanceStatusEnum;
use PHPUnit\Framework\TestCase;

class ChanceStatusEnumTest extends TestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function test_enumValues_areCorrectlyDefined(): void
    {
        $this->assertSame('init', ChanceStatusEnum::INIT->value);
        $this->assertSame('winning', ChanceStatusEnum::WINNING->value);
        $this->assertSame('sent', ChanceStatusEnum::SENT->value);
        $this->assertSame('reviewed', ChanceStatusEnum::REVIEWED->value);
        $this->assertSame('expired', ChanceStatusEnum::EXPIRED->value);
        $this->assertSame('', ChanceStatusEnum::OTHER->value);
    }

    /**
     * 测试标签是否正确返回
     */
    public function test_getLabel_returnsCorrectLabels(): void
    {
        $this->assertSame('未使用', ChanceStatusEnum::INIT->getLabel());
        $this->assertSame('已中奖', ChanceStatusEnum::WINNING->getLabel());
        $this->assertSame('已审核', ChanceStatusEnum::REVIEWED->getLabel());
        $this->assertSame('已发奖', ChanceStatusEnum::SENT->getLabel());
        $this->assertSame('已过期', ChanceStatusEnum::EXPIRED->getLabel());
        $this->assertSame('', ChanceStatusEnum::OTHER->getLabel());
    }
} 