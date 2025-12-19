<?php

declare(strict_types=1);

namespace LotteryBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetAllLotteryChanceParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动ID')]
        public string $activityId,
        #[MethodParam(description: '条数')]
        public int $pageSize = 50,
    ) {
    }
}
