<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Param\GetLotteryDetailParam;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '获取抽奖活动详情')]
#[MethodExpose(method: 'GetLotteryDetail')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetLotteryDetail extends CacheableProcedure
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ChanceRepository $chanceRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @phpstan-param GetLotteryDetailParam $param
     */
    public function execute(GetLotteryDetailParam|RpcParamInterface $param): ArrayResult
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $param->activityId,
            'valid' => true,
        ]);
        if (null === $activity) {
            throw new ApiException('活动无效');
        }

        $result = $activity->retrievePlainArray();
        $result['validChanceCount'] = 0; // 当前有效的抽奖次数

        if (null !== $this->security->getUser()) {
            $c = $this->chanceRepository->createQueryBuilder('a')
                ->select('COUNT(a.id)')
                ->where('a.user = :user AND a.activity = :activity AND a.valid = true and a.expireTime > :now')
                ->setParameter('user', $this->security->getUser())
                ->setParameter('activity', $activity)
                ->setParameter('now', CarbonImmutable::now())
                ->getQuery()
                ->getSingleScalarResult()
            ;
            $result['validChanceCount'] = intval($c);
        }

        return new ArrayResult($result);
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            $key = $this::class . '-no-params';
        } else {
            $key = $this->buildParamCacheKey($params);
        }

        if (null !== $this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 10;
    }

    /**
     * @return iterable<string>
     */
    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield 'lottery-detail';
    }
}
