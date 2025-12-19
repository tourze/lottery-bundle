<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\GetAllLotteryChanceParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetAllLotteryChanceParam::class)]
final class GetAllLotteryChanceParamTest extends TestCase
{

    public function testParamCanBeConstructed(): void
    {
        $param = new GetAllLotteryChanceParam('test-activity', 100);
        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-activity', $param->activityId);
        $this->assertSame(100, $param->pageSize);
    }

    public function testParamWithDefaults(): void
    {
        $param = new GetAllLotteryChanceParam('test-activity');
        $this->assertSame('test-activity', $param->activityId);
        $this->assertSame(50, $param->pageSize);
    }
}