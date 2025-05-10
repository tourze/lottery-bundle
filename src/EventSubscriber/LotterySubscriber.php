<?php

namespace LotteryBundle\EventSubscriber;

use AntdCpBundle\Event\CreateRecordEvent;
use AntdCpBundle\Event\ModifyRecordEvent;
use Carbon\Carbon;
use LotteryBundle\Entity\ActivityAttribute;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Event\ChanceEvent;
use LotteryBundle\Service\LotteryService;
use Symfony\Component\DependencyInjection\Attribute\AutowireServiceClosure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * 理论上，这个可以在分配机会时，调用方去处理
 */
class LotterySubscriber
{
    public function __construct(
        private readonly LotteryService $luckyService,
        #[AutowireServiceClosure('snc_redis.messenger')] private readonly \Closure $redis,
    ) {
    }

    #[AsEventListener]
    public function onChance(ChanceEvent $event): void
    {
        /** @var ActivityAttribute $attribute */
        foreach ($event->getChance()->getActivity()->getAttributes() as $attribute) {
            // 抽奖机会有效期处理 expired:day
            if ('expired' === $attribute->getName()) {
                $startTime = $event->getChance()->getStartTime();
                switch ($attribute->getValue()) {
                    case 'day':
                        $event->getChance()->setExpireTime(Carbon::parse($startTime)->endOfDay());
                        break;
                    case 'month':
                        $event->getChance()->setExpireTime(Carbon::parse($startTime)->addMonth()->endOfDay());
                        break;
                    default:
                        $time = Carbon::parse($attribute->getValue());
                        if ($time) {
                            $event->getChance()->setExpireTime($time);
                        }
                        break;
                }
            }
        }
    }

    /**
     * 补充缓存，用于抽奖
     */
    #[AsEventListener(event: CreateRecordEvent::class)]
    #[AsEventListener(event: ModifyRecordEvent::class)]
    public function buildPrizeRedis(ModifyRecordEvent|CreateRecordEvent $event): void
    {
        $entity = $event->getModel();

        if (!$entity instanceof Prize) {
            return;
        }

        $form = $event->getForm();
        $this->getRedis()->set($this->luckyService::LOTTERY_PRIZE_REDIS_COUNT . $entity->getId(), intval($form['quantity']), 60 * 60 * 24 * 3);
    }

    private function getRedis(): \Redis
    {
        return ($this->redis)();
    }
}
