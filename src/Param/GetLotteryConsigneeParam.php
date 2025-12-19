<?php

declare(strict_types=1);

namespace LotteryBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetLotteryConsigneeParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动ID')]
        public int $chanceId,
    ) {
    }
}
