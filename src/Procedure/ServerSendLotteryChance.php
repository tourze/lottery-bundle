<?php

namespace LotteryBundle\Procedure;

use AppBundle\Service\UserService;
use Carbon\Carbon;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Service\LotteryService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag('抽奖模块')]
#[Log]
#[MethodDoc('服务端发送抽奖机会')]
#[MethodExpose('ServerSendLotteryChance')]
class ServerSendLotteryChance extends LockableProcedure
{
    #[MethodParam('抽奖活动ID')]
    public int $activityId;

    #[MethodParam('用户唯一标志')]
    public string $userIdentity;

    #[MethodParam('获得机会的说明信息')]
    public string $title = '';

    #[MethodParam('有效开始时间，不传入则不限制')]
    public string $startTime;

    #[MethodParam('结束时间，不传入则不限制')]
    public string $expireTime;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly LotteryService $lotteryService,
        private readonly UserService $userService,
    ) {
    }

    public function execute(): array
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $this->activityId,
            'valid' => true,
        ]);
        if (!$activity) {
            throw new ApiException('找不到抽奖活动');
        }

        $user = $this->userService->findUserByIdentity($this->userIdentity);
        if (!$user) {
            throw new ApiException('找不到用户信息');
        }

        $chance = new Chance();
        $chance->setTitle($this->title);
        $chance->setActivity($activity);
        $chance->setValid(true);
        if ($this->startTime) {
            $chance->setStartTime(Carbon::parse($this->startTime));
        }

        if ($this->expireTime) {
            $chance->setExpireTime(Carbon::parse($this->expireTime));
        }

        $chance->setUser($user);

        $this->lotteryService->giveChance($user, $chance);

        return [
            'chance' => [
                'id' => $chance->getId(),
            ],
        ];
    }
}
