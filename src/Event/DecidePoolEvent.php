<?php

namespace LotteryBundle\Event;

use AppBundle\Entity\BizUser;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use Symfony\Contracts\EventDispatcher\Event;

class DecidePoolEvent extends Event
{
    private ?Chance $chance = null;

    private ?Pool $pool = null;

    private ?BizUser $user = null;

    private ?Activity $activity = null;

    public function getChance(): ?Chance
    {
        return $this->chance;
    }

    public function setChance(?Chance $chance): void
    {
        $this->chance = $chance;
    }

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): void
    {
        $this->pool = $pool;
    }

    public function getUser(): ?BizUser
    {
        return $this->user;
    }

    public function setUser(?BizUser $user): void
    {
        $this->user = $user;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): void
    {
        $this->activity = $activity;
    }
}
