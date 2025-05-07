<?php

namespace LotteryBundle\Event;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Prize;
use Symfony\Contracts\EventDispatcher\Event;

class DecidePrizeProbabilityEvent extends Event
{
    private Chance $chance;

    private Prize $prize;

    private int|float $rate;

    public function getChance(): Chance
    {
        return $this->chance;
    }

    public function setChance(Chance $chance): void
    {
        $this->chance = $chance;
    }

    public function getPrize(): Prize
    {
        return $this->prize;
    }

    public function setPrize(Prize $prize): void
    {
        $this->prize = $prize;
    }

    public function getRate(): float|int
    {
        return $this->rate;
    }

    public function setRate(float|int $rate): void
    {
        $this->rate = $rate;
    }
}
