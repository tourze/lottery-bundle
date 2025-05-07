<?php

namespace LotteryBundle\Event;

use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use Tourze\UserEventBundle\Event\UserInteractionContext;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

/**
 * 每抽一次，就触发一次这个事件
 */
class UserJoinSuccessEvent extends UserInteractionEvent implements UserInteractionContext
{
    private Chance $chance;

    private Activity $activity;

    public function getContext(): array
    {
        return [
            'chance' => $this->getChance(),
            'activity' => $this->getActivity(),
        ];
    }

    public function getChance(): Chance
    {
        return $this->chance;
    }

    public function setChance(Chance $chance): void
    {
        $this->chance = $chance;
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): void
    {
        $this->activity = $activity;
    }
}
