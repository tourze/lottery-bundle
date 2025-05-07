<?php

namespace LotteryBundle\Message;

use Tourze\Symfony\Async\Message\AsyncMessageInterface;

/**
 * 异步发送奖励
 */
class SendPrizeMessage implements AsyncMessageInterface
{
    private string $chanceId;

    public function getChanceId(): string
    {
        return $this->chanceId;
    }

    public function setChanceId(string $chanceId): void
    {
        $this->chanceId = $chanceId;
    }
}
