<?php

declare(strict_types=1);

namespace LotteryBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class SaveOrUpdateLotteryConsigneeParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '抽奖机会id')]
        public int $chanceId,
        #[MethodParam(description: '姓名')]
        public string $realName,
        #[MethodParam(description: '手机号')]
        public string $mobile,
        #[MethodParam(description: '地址')]
        public string $address,
        #[MethodParam(description: '抽奖地址ID')]
        public int $consigneeId = 0,
    ) {
    }
}
