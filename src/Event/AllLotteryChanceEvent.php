<?php

namespace LotteryBundle\Event;

use AppBundle\Entity\BizUser;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 全部抽奖记录的额外处理
 */
class AllLotteryChanceEvent extends Event
{
    private QueryBuilder $queryBuilder;

    private BizUser $user;

    private string $activityId;

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getUser(): BizUser
    {
        return $this->user;
    }

    public function setUser(BizUser $user): void
    {
        $this->user = $user;
    }

    public function getActivityId(): string
    {
        return $this->activityId;
    }

    public function setActivityId(string $activityId): void
    {
        $this->activityId = $activityId;
    }
}
