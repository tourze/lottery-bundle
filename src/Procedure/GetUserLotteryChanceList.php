<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\Criteria;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('抽奖模块')]
#[MethodDoc('获取用户抽奖记录列表')]
#[MethodExpose('GetUserLotteryChanceList')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetUserLotteryChanceList extends BaseProcedure
{
    #[MethodParam('活动ID')]
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

        $chance = $this->chanceRepository->findBy([
            'user' => $this->security->getUser(),
            'activity' => $activity,
        ], ['id' => 'desc']);

        // 已使用抽奖机会过滤掉不展示的商品
        $usedChance = $this->chanceRepository->createQueryBuilder('c')
            ->leftJoin('c.prize', 'p')
            ->where('c.user = :user and c.activity = :activity and c.valid = false and c.useTime is not null and p.canShowPrize = true')
            ->setParameter('user', $this->security->getUser())
            ->setParameter('activity', $activity)
            ->orderBy('c.id', Criteria::DESC)
            ->getQuery()
            ->getResult();

        $unUsedChance = $this->chanceRepository->createQueryBuilder('c')
            ->where('c.user = :user and c.activity = :activity and c.valid = true')
            ->andWhere('c.startTime <= :now and c.expireTime >= :now')
            ->setParameter('user', $this->security->getUser())
            ->setParameter('activity', $activity)
            ->setParameter('now', CarbonImmutable::now())
            ->getQuery()
            ->getResult();

        $all = [];
        $used = [];
        $unUsed = [];

        foreach ($chance as $item) {
            $all[] = $item->retrieveApiArray();
        }
        foreach ($usedChance as $item) {
            $used[] = $item->retrieveApiArray();
        }
        foreach ($unUsedChance as $item) {
            $unUsed[] = $item->retrieveApiArray();
        }

        return [
            'all' => $all,
            'used' => $used,
            'unUsed' => $unUsed,
            'activity' => $activity->retrievePlainArray(),
            'canRedeem' => CarbonImmutable::now() <= $activity->getLastRedeemTime(),
        ];
    }
}
