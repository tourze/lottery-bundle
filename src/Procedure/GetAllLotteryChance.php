<?php

namespace LotteryBundle\Procedure;

use Doctrine\Common\Collections\Criteria;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\AllLotteryChanceEvent;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Service\TextResourceProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
#[MethodDoc('获取所有的抽奖记录列表（展示用）')]
#[MethodExpose('GetAllLotteryChance')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetAllLotteryChance extends CacheableProcedure
{
    #[MethodParam('活动ID')]
    public string $activityId;

    #[MethodParam('条数')]
    public int $pageSize = 50;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ChanceRepository $chanceRepository,
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
        if (!$activity) {
            throw new ApiException('活动无效');
        }

        $qb = $this->chanceRepository->createQueryBuilder('c')
            ->leftJoin('c.prize', 'p')
            ->where('c.activity = :activity and c.valid = :valid and p.canShow = :canShow')
            ->setParameter('activity', $activity)
            ->setParameter('valid', false)
            ->setParameter('canShow', true)
            ->setMaxResults($this->pageSize)
            ->orderBy('c.id', Criteria::DESC);

        $event = new AllLotteryChanceEvent();
        $event->setUser($this->security->getUser());
        $event->setQueryBuilder($qb);
        $event->setActivityId($activity->getId());
        $this->eventDispatcher->dispatch($event);

        $chance = $event->getQueryBuilder()->getQuery()->getResult();

        $list = [];
        /** @var Chance $item */
        foreach ($chance as $item) {
            // 过滤掉文字奖品，默认文字奖品为不中奖
            if (empty($item->getPrize()) || TextResourceProvider::CODE === $item->getPrize()->getType()) {
                continue;
            }

            $tmp = $item->retrieveApiArray();
            // 这里不返回地址用户等敏感信息
            unset($tmp['consignee']);
            unset($tmp['user']);
            $str = $item->getUser()->getNickName();
            $tmp['nick_name'] = mb_substr($str, 0, 1) . '**' . mb_substr($str, -1, 1);
            $list[] = $tmp;
        }

        return $list;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Chance::class);
        yield CacheHelper::getClassTags(Activity::class);
    }
}
