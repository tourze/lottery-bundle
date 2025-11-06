<?php

namespace LotteryBundle\Procedure;

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

#[MethodTag(name: '抽奖模块')]
#[MethodExpose(method: 'GetLotteryPrizeList')]
#[MethodDoc(summary: '获取抽奖奖品列表')]
class GetLotteryPrizeList extends CacheableProcedure
{
    #[MethodParam(description: '活动ID')]
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
        $activity = $this->validateActivity();
        $pool = $this->determinePool($activity);
        $prizes = $this->fetchPrizes($pool);
        $list = $this->formatPrizeList($prizes);

        return ['data' => $list];
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

    private function determinePool(Activity $activity): mixed
    {
        $decidePoolEvent = new DecidePoolEvent();
        $decidePoolEvent->setActivity($activity);
        $currentUser = $this->security->getUser();
        if (null !== $currentUser) {
            $decidePoolEvent->setUser($currentUser);
        }
        $this->eventDispatcher->dispatch($decidePoolEvent);
        $pool = $decidePoolEvent->getPool();

        if (null === $pool) {
            $pool = $this->findFirstPoolForActivity();
        }
        if (null === $pool) {
            throw new ApiException('暂无奖品');
        }

        return $pool;
    }

    private function findFirstPoolForActivity(): mixed
    {
        $pools = $this->poolRepository->createQueryBuilder('p')
            ->leftJoin('p.activities', 'a')
            ->where('a.id = :activityId')
            ->setParameter('activityId', $this->activityId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
        if (is_array($pools) && count($pools) > 0) {
            return $pools[0];
        }

        return null;
    }

    /**
     * @return array<Prize>
     */
    private function fetchPrizes(mixed $pool): array
    {
        /** @var array<Prize> $result */
        $result = $this->prizeRepository->createQueryBuilder('p')
            ->where('p.pool = :pool and p.valid = 1 and p.type not in (:type)')
            ->setParameter('pool', $pool)
            ->setParameter('type', TextResourceProvider::CODE)
            ->orderBy('p.sortNumber', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @param array<Prize> $prizes
     * @return array<array<string, mixed>>
     */
    private function formatPrizeList(array $prizes): array
    {
        $list = [];
        foreach ($prizes as $item) {
            if (!$item instanceof Prize) {
                continue;
            }
            $list[] = $item->retrievePlainArray();
        }

        return $list;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            $key = $this::class . '-no-params';
        } else {
            $key = $this->buildParamCacheKey($params);
        }

        $user = $this->security->getUser();
        if (null !== $user) {
            $key .= '-' . $user->getUserIdentifier();
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
