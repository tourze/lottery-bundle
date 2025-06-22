<?php

namespace LotteryBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use LotteryBundle\Entity\ActivityAttribute;
use LotteryBundle\Event\ChanceEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * 理论上，这个可以在分配机会时，调用方去处理
 */
class LotterySubscriber
{
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
                        $event->getChance()->setExpireTime(CarbonImmutable::parse($startTime)->endOfDay());
                        break;
                    case 'month':
                        $event->getChance()->setExpireTime(CarbonImmutable::parse($startTime)->addMonth()->endOfDay());
                        break;
                    default:
                        $time = CarbonImmutable::parse($attribute->getValue());
                        $event->getChance()->setExpireTime($time);
                        break;
                }
            }
        }
    }
}
