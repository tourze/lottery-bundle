<?php

namespace LotteryBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ChanceStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    case INIT = 'init';
    case WINNING = 'winning';
    case SENT = 'sent';
    case REVIEWED = 'reviewed';
    case EXPIRED = 'expired';

    public function getLabel(): string
    {
        return match ($this) {
            self::INIT => '初始化',
            self::WINNING => '中奖',
            self::SENT => '已发送',
            self::REVIEWED => '已审核',
            self::EXPIRED => '已过期',
        };
    }
}
