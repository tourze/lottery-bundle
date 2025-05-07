<?php

namespace LotteryBundle\Event;

use LotteryBundle\Entity\Chance;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 抽奖机会的额外处理
 */
class ChanceEvent extends Event
{
    private Chance $chance;

    public function getChance(): Chance
    {
        return $this->chance;
    }

    public function setChance(Chance $chance): void
    {
        $this->chance = $chance;
    }
}
