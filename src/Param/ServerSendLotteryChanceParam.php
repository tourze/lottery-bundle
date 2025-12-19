<?php

declare(strict_types=1);

namespace LotteryBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ServerSendLotteryChanceParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '抽奖活动ID')]
        public int $activityId,
        #[MethodParam(description: '用户唯一标志')]
        public string $userIdentity,
        #[MethodParam(description: '获得机会的说明信息')]
        public string $title = '',
        #[MethodParam(description: '有效开始时间,不传入则不限制')]
        public string $startTime = '',
        #[MethodParam(description: '结束时间,不传入则不限制')]
        public string $expireTime = '',
    ) {
    }
}
