<?php

declare(strict_types=1);

namespace LotteryBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class JoinLotteryParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '活动ID')]
        public int $activityId,
        #[MethodParam(description: '连续抽取次数')]
        public int $count = 1,
    ) {
    }
}
