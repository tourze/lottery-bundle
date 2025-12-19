<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Param\GetUserValidLotteryChanceCountsParam;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '获取用户可用抽奖次数')]
#[MethodExpose(method: 'GetUserValidLotteryChanceCounts')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetUserValidLotteryChanceCounts extends BaseProcedure
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly Security $security,
        private readonly ChanceRepository $chanceRepository,
    ) {
    }

    /**
     * @phpstan-param GetUserValidLotteryChanceCountsParam $param
     */
    public function execute(GetUserValidLotteryChanceCountsParam|RpcParamInterface $param): ArrayResult
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $param->activityId,
            'valid' => true,
        ]);
        if (null === $activity) {
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
            ->getSingleScalarResult()
        ;

        return new ArrayResult([
            'count' => $unUsedChance,
        ]);
    }
}
