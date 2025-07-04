<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '获取抽奖活动详情')]
#[MethodExpose(method: 'GetLotteryDetail')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetLotteryDetail extends CacheableProcedure
{
    #[MethodParam(description: '活动ID')]
    public string $activityId;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ChanceRepository $chanceRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $this->activityId,
            'valid' => true,
        ]);
        if ($activity === null) {
            throw new ApiException('活动无效');
        }

        $result = $activity->retrievePlainArray();
        $result['validChanceCount'] = 0; // 当前有效的抽奖次数

        if ($this->security->getUser() !== null) {
            $c = $this->chanceRepository->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->where('a.user = :user AND a.activity = :activity AND a.valid = true and a.expireTime > :now')
                ->setParameter('user', $this->security->getUser())
                ->setParameter('activity', $activity)
                ->setParameter('now', CarbonImmutable::now())
                ->getQuery()
                ->getSingleScalarResult();
            $result['validChanceCount'] = intval($c);
        }

        return $result;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser() !== null) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 10;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield null;
    }
}
