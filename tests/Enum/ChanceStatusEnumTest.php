<?php

namespace LotteryBundle\Tests\Enum;

use LotteryBundle\Enum\ChanceStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ChanceStatusEnum::class)]
final class ChanceStatusEnumTest extends AbstractEnumTestCase
{
    /**
     * 测试枚举值是否正确定义
     */
    public function testEnumValuesAreCorrectlyDefined(): void
    {
        $this->assertSame('init', ChanceStatusEnum::INIT->value);
        $this->assertSame('winning', ChanceStatusEnum::WINNING->value);
        $this->assertSame('sent', ChanceStatusEnum::SENT->value);
        $this->assertSame('reviewed', ChanceStatusEnum::REVIEWED->value);
        $this->assertSame('expired', ChanceStatusEnum::EXPIRED->value);
    }

    /**
     * 测试标签是否正确返回
     */
    public function testGetLabelReturnsCorrectLabels(): void
    {
        $this->assertSame('初始化', ChanceStatusEnum::INIT->getLabel());
        $this->assertSame('中奖', ChanceStatusEnum::WINNING->getLabel());
        $this->assertSame('已审核', ChanceStatusEnum::REVIEWED->getLabel());
        $this->assertSame('已发送', ChanceStatusEnum::SENT->getLabel());
        $this->assertSame('已过期', ChanceStatusEnum::EXPIRED->getLabel());
    }

    /**
     * 测试 toArray 方法
     */
    public function testToArray(): void
    {
        // toArray 是实例方法，需要在枚举实例上调用
        $initCase = ChanceStatusEnum::INIT;
        $result = $initCase->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertEquals('init', $result['value']);
        $this->assertEquals('初始化', $result['label']);

        // 测试其他枚举值
        $winningCase = ChanceStatusEnum::WINNING;
        $result = $winningCase->toArray();
        $this->assertEquals('winning', $result['value']);
        $this->assertEquals('中奖', $result['label']);
    }

    /**
     * 测试 toSelectItem 方法
     */
    public function testEnumSelectItemConversion(): void
    {
        $initCase = ChanceStatusEnum::INIT;
        $result = $initCase->toSelectItem();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);
        $this->assertEquals('init', $result['value']);
        $this->assertEquals('初始化', $result['label']);
    }

    /**
     * 测试所有枚举值
     */
    public function testAllEnumValues(): void
    {
        $cases = ChanceStatusEnum::cases();

        $this->assertCount(5, $cases);
        $this->assertContains(ChanceStatusEnum::INIT, $cases);
        $this->assertContains(ChanceStatusEnum::WINNING, $cases);
        $this->assertContains(ChanceStatusEnum::SENT, $cases);
        $this->assertContains(ChanceStatusEnum::REVIEWED, $cases);
        $this->assertContains(ChanceStatusEnum::EXPIRED, $cases);
    }
}
