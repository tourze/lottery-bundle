<?php

namespace LotteryBundle\Service;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Event\DecidePoolEvent;
use LotteryBundle\Exception\LotteryException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PoolService
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function dispatch(Chance $chance): void
    {
        // 奖池判断事件
        $event = new DecidePoolEvent();
        $event->setActivity($chance->getActivity());
        $event->setChance($chance);
        $event->setUser($chance->getUser());
        $this->eventDispatcher->dispatch($event);
        if ($chance->getPool() !== null) {
            return;
        }

        // 如果上面的事件，没确认奖池，我们就进入随机奖池
        $pools = $chance->getActivity()->getPools()
            ->filter(fn (Pool $item) => $item->isValid())
            ->toArray();
        $pools = array_values($pools);
        $c = count($pools);
        if (0 === $c) {
            throw new LotteryException('请联系管理员配置奖池');
        }

        // 先随机选一个奖池
        $rIndex = random_int(0, $c - 1);
        $pool = $pools[$rIndex];
        $chance->setPool($pool);
        $chance->setPoolContext([
            'type' => 'random',
            'count' => $c,
            'index' => $rIndex,
        ]);
    }
}
