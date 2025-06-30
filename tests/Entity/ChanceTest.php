<?php

namespace LotteryBundle\Tests\Entity;

use BenefitBundle\Model\BenefitResource;
use Doctrine\Common\Collections\Collection;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use LotteryBundle\Enum\ChanceStatusEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;

class ChanceTest extends TestCase
{
    public function test_constructor_setsDefaultValues(): void
    {
        $chance = new Chance();

        $this->assertEquals(0, $chance->getId());
        $this->assertEquals('', $chance->getTitle());
        $this->assertEquals(ChanceStatusEnum::INIT, $chance->getStatus());
        $this->assertInstanceOf(Collection::class, $chance->getStocks());
        $this->assertCount(0, $chance->getStocks());
        $this->assertEquals([], $chance->getSendResult());
        $this->assertEquals([], $chance->getPoolContext());
        $this->assertEquals([], $chance->getProbabilityContext());
    }

    public function test_implements_required_interfaces(): void
    {
        $chance = new Chance();

        $this->assertInstanceOf(PlainArrayInterface::class, $chance);
        $this->assertInstanceOf(ApiArrayInterface::class, $chance);
        $this->assertInstanceOf(AdminArrayInterface::class, $chance);
        $this->assertInstanceOf(\Stringable::class, $chance);
        $this->assertInstanceOf(BenefitResource::class, $chance);
    }

    public function test_setTitle_setsAndReturnsTitle(): void
    {
        $chance = new Chance();
        $title = 'Test Title';

        $result = $chance->setTitle($title);

        $this->assertEquals($title, $chance->getTitle());
        $this->assertSame($chance, $result);
    }

    public function test_setTitle_withNull_setsEmpty(): void
    {
        $chance = new Chance();

        $result = $chance->setTitle(null);

        $this->assertEquals('', $chance->getTitle());
        $this->assertSame($chance, $result);
    }

    public function test_startTime_setterAndGetter(): void
    {
        $chance = new Chance();
        $startTime = new \DateTimeImmutable();

        $result = $chance->setStartTime($startTime);

        $this->assertSame($startTime, $chance->getStartTime());
        $this->assertSame($chance, $result);
    }

    public function test_expireTime_setterAndGetter(): void
    {
        $chance = new Chance();
        $expireTime = new \DateTimeImmutable();

        $result = $chance->setExpireTime($expireTime);

        $this->assertSame($expireTime, $chance->getExpireTime());
        $this->assertSame($chance, $result);
    }

    public function test_useTime_setterAndGetter(): void
    {
        $chance = new Chance();
        $useTime = new \DateTimeImmutable();

        $result = $chance->setUseTime($useTime);

        $this->assertSame($useTime, $chance->getUseTime());
        $this->assertSame($chance, $result);
    }

    public function test_sendTime_setterAndGetter(): void
    {
        $chance = new Chance();
        $sendTime = new \DateTimeImmutable();

        $result = $chance->setSendTime($sendTime);

        $this->assertSame($sendTime, $chance->getSendTime());
        $this->assertSame($chance, $result);
    }

    public function test_remark_setterAndGetter(): void
    {
        $chance = new Chance();
        $remark = 'test remark';

        $result = $chance->setRemark($remark);

        $this->assertEquals($remark, $chance->getRemark());
        $this->assertSame($chance, $result);
    }

    public function test_valid_setterAndGetter(): void
    {
        $chance = new Chance();

        $result = $chance->setValid(true);

        $this->assertTrue($chance->getValid());
        $this->assertSame($chance, $result);
    }

    public function test_status_setterAndGetter(): void
    {
        $chance = new Chance();

        $result = $chance->setStatus(ChanceStatusEnum::SENT);

        $this->assertEquals(ChanceStatusEnum::SENT, $chance->getStatus());
        $this->assertSame($chance, $result);
    }

    public function test_activity_setterAndGetter(): void
    {
        $chance = new Chance();
        $activity = $this->createMock(Activity::class);

        $result = $chance->setActivity($activity);

        $this->assertSame($activity, $chance->getActivity());
        $this->assertSame($chance, $result);
    }

    public function test_user_setterAndGetter(): void
    {
        $chance = new Chance();
        $user = $this->createMock(UserInterface::class);

        $result = $chance->setUser($user);

        $this->assertSame($user, $chance->getUser());
        $this->assertSame($chance, $result);
    }

    public function test_pool_setterAndGetter(): void
    {
        $chance = new Chance();
        $pool = $this->createMock(Pool::class);

        $result = $chance->setPool($pool);

        $this->assertSame($pool, $chance->getPool());
        $this->assertSame($chance, $result);
    }

    public function test_prize_setterAndGetter(): void
    {
        $chance = new Chance();
        $prize = $this->createMock(Prize::class);

        $result = $chance->setPrize($prize);

        $this->assertSame($prize, $chance->getPrize());
        $this->assertSame($chance, $result);
    }

    public function test_consignee_setterAndGetter(): void
    {
        $chance = new Chance();
        $consignee = $this->createMock(Consignee::class);

        $result = $chance->setConsignee($consignee);

        $this->assertSame($consignee, $chance->getConsignee());
        $this->assertSame($chance, $result);
    }

    public function test_stocks_addAndRemove(): void
    {
        $chance = new Chance();
        $stock = $this->createMock(Stock::class);

        // 模拟 Stock 的 setChance 方法 
        $stock->expects($this->exactly(2))
            ->method('setChance')
            ->with($this->logicalOr($chance, null));

        $result = $chance->addStock($stock);

        $this->assertTrue($chance->getStocks()->contains($stock));
        $this->assertSame($chance, $result);

        // 测试移除
        $stock->expects($this->once())
            ->method('getChance')
            ->willReturn($chance);

        $removeResult = $chance->removeStock($stock);

        $this->assertFalse($chance->getStocks()->contains($stock));
        $this->assertSame($chance, $removeResult);
    }

    public function test_stocks_addDuplicate_doesNotDuplicate(): void
    {
        $chance = new Chance();
        $stock = $this->createMock(Stock::class);

        $stock->expects($this->once())
            ->method('setChance')
            ->with($chance);

        $chance->addStock($stock);
        $chance->addStock($stock); // 添加相同的库存

        $this->assertCount(1, $chance->getStocks());
    }

    public function test_sendResult_setterAndGetter(): void
    {
        $chance = new Chance();
        $sendResult = ['status' => 'sent', 'timestamp' => time()];

        $result = $chance->setSendResult($sendResult);

        $this->assertEquals($sendResult, $chance->getSendResult());
        $this->assertSame($chance, $result);
    }

    public function test_poolContext_setterAndGetter(): void
    {
        $chance = new Chance();
        $poolContext = ['pool_id' => 1, 'selected_at' => time()];

        $result = $chance->setPoolContext($poolContext);

        $this->assertEquals($poolContext, $chance->getPoolContext());
        $this->assertSame($chance, $result);
    }

    public function test_probabilityContext_setterAndGetter(): void
    {
        $chance = new Chance();
        $probabilityContext = ['calculated_probability' => 0.15, 'factors' => []];

        $result = $chance->setProbabilityContext($probabilityContext);

        $this->assertEquals($probabilityContext, $chance->getProbabilityContext());
        $this->assertSame($chance, $result);
    }

    public function test_reviewTime_setterAndGetter(): void
    {
        $chance = new Chance();
        $reviewTime = '2025-01-19 10:00:00';

        $result = $chance->setReviewTime($reviewTime);

        $this->assertEquals($reviewTime, $chance->getReviewTime());
        $this->assertSame($chance, $result);
    }

    public function test_reviewUser_setterAndGetter(): void
    {
        $chance = new Chance();
        $reviewUser = $this->createMock(UserInterface::class);

        $result = $chance->setReviewUser($reviewUser);

        $this->assertSame($reviewUser, $chance->getReviewUser());
        $this->assertSame($chance, $result);
    }

    public function test_lockVersion_setterAndGetter(): void
    {
        $chance = new Chance();
        $lockVersion = 5;

        $result = $chance->setLockVersion($lockVersion);

        $this->assertEquals($lockVersion, $chance->getLockVersion());
        $this->assertSame($chance, $result);
    }

    public function test_ipFields_settersAndGetters(): void
    {
        $chance = new Chance();
        $createIp = '192.168.1.1';
        $updateIp = '192.168.1.2';

        $result1 = $chance->setCreatedFromIp($createIp);
        $result2 = $chance->setUpdatedFromIp($updateIp);

        $this->assertEquals($createIp, $chance->getCreatedFromIp());
        $this->assertEquals($updateIp, $chance->getUpdatedFromIp());
        $this->assertSame($chance, $result1);
        $this->assertSame($chance, $result2);
    }

    public function test_toString_withNullOrZeroId_returnsEmptyString(): void
    {
        $chance = new Chance();

        $this->assertEquals('', (string) $chance);
    }

    public function test_toString_withValidId_returnsFormattedString(): void
    {
        $chance = new Chance();

        // 通过反射设置ID值
        $reflection = new \ReflectionClass($chance);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($chance, 123);

        $result = (string) $chance;
        $this->assertStringContainsString('Chance-123', $result);
    }
}
