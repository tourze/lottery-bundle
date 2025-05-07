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
    case OTHER = ''; // 兼容历史数据无状态的情况

    public function getLabel(): string
    {
        return match ($this) {
            self::INIT => '未使用',
            self::WINNING => '已中奖',
            self::REVIEWED => '已审核',
            self::SENT => '已发奖',
            self::EXPIRED => '已过期',
            self::OTHER => '',
        };
    }
}
