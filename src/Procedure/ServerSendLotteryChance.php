<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Service\LotteryService;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '抽奖模块')]
#[Log]
#[MethodDoc(summary: '服务端发送抽奖机会')]
#[MethodExpose(method: 'ServerSendLotteryChance')]
class ServerSendLotteryChance extends LockableProcedure
{
    #[MethodParam(description: '抽奖活动ID')]
    public int $activityId;

    #[MethodParam(description: '用户唯一标志')]
    public string $userIdentity;

    #[MethodParam(description: '获得机会的说明信息')]
    public string $title = '';

    #[MethodParam(description: '有效开始时间，不传入则不限制')]
    public string $startTime;

    #[MethodParam(description: '结束时间，不传入则不限制')]
    public string $expireTime;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly LotteryService $lotteryService,
        private readonly UserLoaderInterface $userLoader,
    ) {
    }

    public function execute(): array
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $this->activityId,
            'valid' => true,
        ]);
        if ($activity === null) {
            throw new ApiException('找不到抽奖活动');
        }

        $user = $this->userLoader->loadUserByIdentifier($this->userIdentity);
        if ($user === null) {
            throw new ApiException('找不到用户信息');
        }

        $chance = new Chance();
        $chance->setTitle($this->title);
        $chance->setActivity($activity);
        $chance->setValid(true);
        if ($this->startTime !== '') {
            $chance->setStartTime(CarbonImmutable::parse($this->startTime));
        }

        if ($this->expireTime !== '') {
            $chance->setExpireTime(CarbonImmutable::parse($this->expireTime));
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
