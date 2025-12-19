<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\GetLotteryPrizeListParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetLotteryPrizeListParam::class)]
final class GetLotteryPrizeListParamTest extends TestCase
{

    public function testParamCanBeConstructed(): void
    {
        $param = new GetLotteryPrizeListParam('test');
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }
}
