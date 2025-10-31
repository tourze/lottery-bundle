<?php

namespace LotteryBundle\Exception;

/**
 * 奖品服务异常类
 * 用于替换 PrizeService 中的 RuntimeException
 */
class PrizeServiceException extends \Exception
{
    public static function chancePoolCannotBeNull(): self
    {
        return new self('Chance pool cannot be null');
    }

    public static function chanceUserCannotBeNull(): self
    {
        return new self('Chance user cannot be null');
    }

    public static function prizeTypeIdCannotBeNull(): self
    {
        return new self('Prize typeId cannot be null');
    }

    public static function chanceActivityCannotBeNull(): self
    {
        return new self('Chance activity cannot be null');
    }
}
