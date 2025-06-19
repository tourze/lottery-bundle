<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('抽奖模块')]
#[MethodDoc('获取用户可用抽奖次数')]
#[MethodExpose('GetUserValidLotteryChanceCounts')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetUserValidLotteryChanceCounts extends BaseProcedure
{
    public int $activityId;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly Security $security,
        private readonly ChanceRepository $chanceRepository,
    ) {
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

        $unUsedChance = $this->chanceRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user and c.activity = :activity and c.valid = true')
            ->andWhere('c.startTime <= :now and c.expireTime >= :now')
            ->setParameter('user', $this->security->getUser())
            ->setParameter('activity', $activity)
            ->setParameter('now', CarbonImmutable::now())
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'count' => $unUsedChance,
        ];
    }
}
