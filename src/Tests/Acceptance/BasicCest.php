<?php

namespace LotteryBundle\Tests\Acceptance;

use App\Tests\AcceptanceTester;
use Carbon\Carbon;
use LotteryBundle\Entity\Chance;

class BasicCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * 抽奖一次
     */
    public function tryToTestLottery(AcceptanceTester $I): void
    {
        $I->amGeneralMember('13800138000');

        // 分配一个机会咯
        $I->haveInRepository(Chance::class, [
            'activity' => 1,
            'valid' => true,
            'startTime' => Carbon::now(),
        ]);
    }
}
