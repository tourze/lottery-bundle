<?php

namespace LotteryBundle\Procedure;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Event\UserJoinSuccessEvent;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Service\LotteryService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\TextManageBundle\Service\TextFormatter;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodTag('抽奖模块')]
#[MethodDoc('开始抽奖')]
#[MethodExpose('JoinLottery')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class JoinLottery extends LockableProcedure
{
    #[MethodParam('活动ID')]
    public int $activityId;

    #[MethodParam('连续抽取次数')]
    public int $count = 1;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ChanceRepository $chanceRepository,
        private readonly LotteryService $luckyService,
        private readonly LoggerInterface $logger,
        private readonly TextFormatter $textFormatter,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        $activity = $this->activityRepository->findOneBy([
            'id' => $this->activityId,
            'valid' => true,
        ]);
        if (!$activity || $activity->getEndTime() < CarbonImmutable::now()) {
            throw new ApiException('活动无效');
        }

        // 获取机会
        /** @var Chance|null $chance */
        $chances = $this->chanceRepository->createQueryBuilder('a')
            ->where('a.user=:user AND a.activity=:activity AND a.valid=true')
            ->setParameter('user', $this->security->getUser())
            ->setParameter('activity', $activity)
            ->setMaxResults($this->count)
            ->getQuery()
            ->getResult();
        if (empty($chances)) {
            throw new ApiException($this->textFormatter->formatText($activity->getNoChanceText() ?: '您已没有抽奖机会', ['activity' => $activity]));
            //            $chance = new Chance(); // todo 用于压测
            //            $chance->setActivity($activity);
            //            $chance->setUser($this->security->getUser());
            //            $chance->setValid(true);
            //            $chance->setTitle('手动生成');
            //            $chance->setStartTime(CarbonImmutable::now());
            //            $chance->setExpireTime(CarbonImmutable::now()->addDays(7));
            //            $this->chanceRepository->save($chance);
        }

        $result = [];
        $i = 1;
        foreach ($chances as $chance) {
            if ($chance->getUseTime()) {
                throw new ApiException('机会已被消耗，请重试');
            }

            // 过期的话，在这里直接设置为过期
            if ($chance->getExpireTime() && CarbonImmutable::now()->greaterThan($chance->getExpireTime())) {
                $chance->setValid(false);
                $this->entityManager->persist($chance);
                $this->entityManager->flush();
                throw new ApiException($this->textFormatter->formatText($activity->getNoChanceText() ?: '您已没有抽奖机会', ['activity' => $activity]));
            }

            try {
                $this->luckyService->doLottery($chance);
            } catch (\Throwable $exception) {
                $this->logger->error('抽奖失败', [
                    'exception' => $exception,
                    'chance' => $chance,
                ]);
                throw new ApiException($exception->getMessage(), previous: $exception);
            }
            $result[] = $chance->retrievePlainArray();

            $event = new UserJoinSuccessEvent();
            $event->setSender($this->security->getUser());
            $event->setReceiver(SystemUser::instance());
            $event->setMessage('抽奖完成，并获得奖励信息');
            $event->setChance($chance);
            $event->setActivity($activity);
            $this->eventDispatcher->dispatch($event);

            ++$i;
            if ($i > $this->count) {
                break;
            }
        }

        return $result;
    }
}
