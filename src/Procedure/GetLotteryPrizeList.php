<?php

namespace LotteryBundle\Procedure;

use Doctrine\Common\Collections\Criteria;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Event\DecidePoolEvent;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\PoolRepository;
use LotteryBundle\Repository\PrizeRepository;
use LotteryBundle\Service\TextResourceProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

#[MethodTag('抽奖模块')]
#[MethodExpose('GetLotteryPrizeList')]
#[MethodDoc('获取抽奖奖品列表')]
class GetLotteryPrizeList extends CacheableProcedure
{
    #[MethodParam('活动ID')]
    public string $activityId;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly PrizeRepository $prizeRepository,
        private readonly PoolRepository $poolRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
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

        $decidePoolEvent = new DecidePoolEvent();
        $decidePoolEvent->setActivity($activity);
        $decidePoolEvent->setUser($this->security->getUser());
        $this->eventDispatcher->dispatch($decidePoolEvent);
        $pool = $decidePoolEvent->getPool();

        if ($pool === null) {
            // 存在多个奖池则取第一个
            $pool = $this->poolRepository->createQueryBuilder('p')
                ->leftJoin('p.activities', 'a')
                ->where('a.id = :activityId')
                ->setParameter('activityId', $this->activityId)
                ->getQuery()
                ->getResult();
        }
        if ($pool === null) {
            throw new ApiException('暂无奖品');
        }

        // 文本类奖品就不展示了
        $prizes = $this->prizeRepository->createQueryBuilder('p')
            ->where('p.pool = :pool and p.valid = 1 and p.type not in (:type)')
            ->setParameter('pool', $pool)
            ->setParameter('type', TextResourceProvider::CODE)
            ->orderBy('p.sortNumber', Criteria::DESC)
            ->getQuery()
            ->getResult();

        $list = [];
        foreach ($prizes as $item) {
            /* @var Prize $item */
            $list[] = $item->retrievePlainArray();
        }

        return $list;
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
        return 60 * 60;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Activity::class);
        yield CacheHelper::getClassTags(Prize::class);
    }
}
