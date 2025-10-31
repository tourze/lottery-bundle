<?php

declare(strict_types=1);

namespace LotteryBundle\Procedure;

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

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '获取所有的抽奖记录列表（展示用）')]
#[MethodExpose(method: 'GetAllLotteryChance')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetAllLotteryChance extends CacheableProcedure
{
    #[MethodParam(description: '活动ID')]
    public string $activityId;

    #[MethodParam(description: '条数')]
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
        if (null === $activity) {
            throw new ApiException('活动无效');
        }

        $qb = $this->chanceRepository->createQueryBuilder('c')
            ->leftJoin('c.prize', 'p')
            ->where('c.activity = :activity and c.valid = :valid and p.canShow = :canShow')
            ->setParameter('activity', $activity)
            ->setParameter('valid', false)
            ->setParameter('canShow', true)
            ->setMaxResults($this->pageSize)
            ->orderBy('c.id', 'DESC')
        ;

        $event = new AllLotteryChanceEvent();
        $currentUser = $this->security->getUser();
        if (null !== $currentUser) {
            $event->setUser($currentUser);
        }
        $event->setQueryBuilder($qb);
        $event->setActivityId((string) $activity->getId());
        $this->eventDispatcher->dispatch($event);

        $chances = $event->getQueryBuilder()->getQuery()->getResult();
        assert(is_array($chances));

        $list = [];
        foreach ($chances as $item) {
            if (!$item instanceof Chance) {
                continue;
            }
            // 过滤掉文字奖品，默认文字奖品为不中奖
            if (null === $item->getPrize() || TextResourceProvider::CODE === $item->getPrize()->getType()) {
                continue;
            }

            $tmp = $item->retrieveApiArray();
            // 这里不返回地址用户等敏感信息
            unset($tmp['consignee'], $tmp['user']);

            $user = $item->getUser();
            if (null === $user) {
                continue;
            }
            $str = method_exists($user, 'getNickName') ? $user->getNickName() : $user->getUserIdentifier();
            assert(is_string($str));

            $tmp['nick_name'] = mb_substr($str, 0, 1) . '**' . mb_substr($str, -1, 1);
            $list[] = $tmp;
        }

        return ['data' => $list];
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
        return 60;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Chance::class);
        yield CacheHelper::getClassTags(Activity::class);
    }
}
