<?php

namespace LotteryBundle\Procedure\Watchword;

use Carbon\Carbon;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\WatchwordLog;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\WatchwordLogRepository;
use LotteryBundle\Repository\WatchwordRepository;
use LotteryBundle\Service\LotteryService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag('抽奖模块')]
#[MethodExpose('SubmitLotteryWatchwordCheck')]
#[MethodDoc('提交抽奖口令，并返回是否得到抽奖机会')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class SubmitLotteryWatchwordCheck extends LockableProcedure
{
    #[MethodParam('活动ID')]
    public string $activityId;

    #[MethodParam('口令')]
    public string $code;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly WatchwordRepository $watchwordRepository,
        private readonly WatchwordLogRepository $logRepository,
        private readonly LotteryService $lotteryService,
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
    ) {
    }

    public static function getMockResult(): ?array
    {
        return [
            '__message' => '口令使用成功',
        ];
    }

    public function execute(): array
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $this->activityId,
            'valid' => true,
        ]);
        if (!$activity) {
            throw new ApiException('活动无效');
        }

        $watchword = $this->watchwordRepository->findOneBy([
            'activity' => $activity,
            'code' => $this->code,
            'valid' => true,
        ]);
        if (!$watchword) {
            throw new ApiException('找不到口令');
        }

        // 如果已经使用过的话，不给重复使用
        $log = $this->logRepository->findOneBy([
            'user' => $this->security->getUser(),
            'watchword' => $watchword,
        ]);
        if ($log) {
            throw new ApiException('您已使用过这个口令，不要重复使用');
        }

        // 检查数量是否超出限制了
        if ($watchword->getMaxCount() > 0) {
            $count = $this->logRepository->count([
                'watchword' => $watchword,
                'valid' => true,
            ]);
            if ($count >= $watchword->getMaxCount()) {
                throw new ApiException('该口令已到达最大使用上限');
            }
        }

        // 保存使用记录
        $log = new WatchwordLog();
        $log->setActivity($activity);
        $log->setWatchword($watchword);
        $log->setUser($this->security->getUser());
        $log->setValid(true);
        $this->doctrineService->directInsert($log);

        // 条件通过，我们创建一个口令使用记录
        $chance = new Chance();
        $chance->setActivity($activity);
        $chance->setValid(true);
        $chance->setStartTime(Carbon::now());
        // TODO 暂时口令赠送的机会，没使用时间上限
        $chance->setTitle("口令：{$watchword->getCode()}");
        $this->lotteryService->giveChance($log->getUser(), $chance);

        return [
            '__message' => '口令使用成功',
        ];
    }
}
