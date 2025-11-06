<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
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

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '获取用户抽奖记录列表')]
#[MethodExpose(method: 'GetUserLotteryChanceList')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetUserLotteryChanceList extends BaseProcedure
{
    #[MethodParam(description: '活动ID')]
    public int $activityId;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly Security $security,
        private readonly ChanceRepository $chanceRepository,
    ) {
    }

    public function execute(): array
    {
        $activity = $this->validateActivity();
        $user = $this->security->getUser();

        $allChances = $this->fetchAllChances($user, $activity);
        $usedChances = $this->fetchUsedChances($user, $activity);
        $unUsedChances = $this->fetchUnUsedChances($user, $activity);

        return [
            'all' => $this->formatChanceList($allChances),
            'used' => $this->formatChanceList($usedChances),
            'unUsed' => $this->formatChanceList($unUsedChances),
            'activity' => $activity->retrievePlainArray(),
            'canRedeem' => $this->canRedeem($activity),
        ];
    }

    private function validateActivity(): Activity
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $this->activityId,
            'valid' => true,
        ]);
        if (null === $activity) {
            throw new ApiException('活动无效');
        }

        return $activity;
    }

    /**
     * @return array<Chance>
     */
    private function fetchAllChances(mixed $user, Activity $activity): array
    {
        /** @var array<Chance> $result */
        $result = $this->chanceRepository->findBy([
            'user' => $user,
            'activity' => $activity,
        ], ['id' => 'desc']);

        return $result;
    }

    /**
     * @return array<Chance>
     */
    private function fetchUsedChances(mixed $user, Activity $activity): array
    {
        /** @var array<Chance> $result */
        $result = $this->chanceRepository->createQueryBuilder('c')
            ->leftJoin('c.prize', 'p')
            ->where(
                'c.user = :user and c.activity = :activity and c.valid = false ' .
                'and c.useTime is not null and p.canShowPrize = true'
            )
            ->setParameter('user', $user)
            ->setParameter('activity', $activity)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @return array<Chance>
     */
    private function fetchUnUsedChances(mixed $user, Activity $activity): array
    {
        /** @var array<Chance> $result */
        $result = $this->chanceRepository->createQueryBuilder('c')
            ->where('c.user = :user and c.activity = :activity and c.valid = true')
            ->andWhere('c.startTime <= :now and c.expireTime >= :now')
            ->setParameter('user', $user)
            ->setParameter('activity', $activity)
            ->setParameter('now', CarbonImmutable::now())
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @param array<Chance> $chances
     * @return array<array<string, mixed>>
     */
    private function formatChanceList(array $chances): array
    {
        $list = [];
        foreach ($chances as $item) {
            if (!$item instanceof Chance) {
                continue;
            }
            $list[] = $item->retrieveApiArray();
        }

        return $list;
    }

    private function canRedeem(Activity $activity): bool
    {
        return null !== $activity->getLastRedeemTime() && CarbonImmutable::now() <= $activity->getLastRedeemTime();
    }
}
