<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\GetLotteryDetailParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetLotteryDetailParam::class)]
final class GetLotteryDetailParamTest extends TestCase
{

    public function testParamCanBeConstructed(): void
    {
        $param = new GetLotteryDetailParam('test');
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }
}
