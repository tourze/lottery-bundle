<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Service\LotteryService;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use LotteryBundle\Param\ServerSendLotteryChanceParam;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '抽奖模块')]
#[Log]
#[MethodDoc(summary: '服务端发送抽奖机会')]
#[MethodExpose(method: 'ServerSendLotteryChance')]
class ServerSendLotteryChance extends LockableProcedure
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly LotteryService $lotteryService,
        private readonly UserLoaderInterface $userLoader,
    ) {
    }

    /**
     * @phpstan-param ServerSendLotteryChanceParam $param
     */
    public function execute(ServerSendLotteryChanceParam|RpcParamInterface $param): ArrayResult
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $param->activityId,
            'valid' => true,
        ]);
        if (null === $activity) {
            throw new ApiException('找不到抽奖活动');
        }

        $user = $this->userLoader->loadUserByIdentifier($param->userIdentity);
        if (null === $user) {
            throw new ApiException('找不到用户信息');
        }

        $chance = new Chance();
        $chance->setTitle($param->title);
        $chance->setActivity($activity);
        $chance->setValid(true);
        if ('' !== $param->startTime) {
            $chance->setStartTime(CarbonImmutable::parse($param->startTime));
        }

        if ('' !== $param->expireTime) {
            $chance->setExpireTime(CarbonImmutable::parse($param->expireTime));
        }

        $chance->setUser($user);

        $this->lotteryService->giveChance($user, $chance);

        return new ArrayResult([
            'chance' => [
                'id' => $chance->getId(),
            ],
        ]);
    }
}
