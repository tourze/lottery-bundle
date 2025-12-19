<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\GetLotteryConsigneeParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetLotteryConsigneeParam::class)]
final class GetLotteryConsigneeParamTest extends TestCase
{

    public function testParamCanBeConstructed(): void
    {
        $param = new GetLotteryConsigneeParam(123);
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }
}
