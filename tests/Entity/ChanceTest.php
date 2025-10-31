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
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Chance::class)]
final class ChanceTest extends AbstractEntityTestCase
{
    protected function createEntity(): Chance
    {
        return new Chance();
    }

    public function testConstructorSetsDefaultValues(): void
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

    public function testImplementsRequiredInterfaces(): void
    {
        $chance = new Chance();

        $this->assertInstanceOf(PlainArrayInterface::class, $chance);
        $this->assertInstanceOf(ApiArrayInterface::class, $chance);
        $this->assertInstanceOf(AdminArrayInterface::class, $chance);
        $this->assertInstanceOf(\Stringable::class, $chance);
        $this->assertInstanceOf(BenefitResource::class, $chance);
    }

    public function testSetTitleSetsAndReturnsTitle(): void
    {
        $chance = new Chance();
        $title = 'Test Title';

        $chance->setTitle($title);

        $this->assertEquals($title, $chance->getTitle());
    }

    public function testSetTitleWithNullSetsEmpty(): void
    {
        $chance = new Chance();

        $chance->setTitle(null);

        $this->assertEquals('', $chance->getTitle());
    }

    public function testStartTimeSetterAndGetter(): void
    {
        $chance = new Chance();
        $startTime = new \DateTimeImmutable();

        $chance->setStartTime($startTime);

        $this->assertSame($startTime, $chance->getStartTime());
    }

    public function testExpireTimeSetterAndGetter(): void
    {
        $chance = new Chance();
        $expireTime = new \DateTimeImmutable();

        $chance->setExpireTime($expireTime);

        $this->assertSame($expireTime, $chance->getExpireTime());
    }

    public function testUseTimeSetterAndGetter(): void
    {
        $chance = new Chance();
        $useTime = new \DateTimeImmutable();

        $chance->setUseTime($useTime);

        $this->assertSame($useTime, $chance->getUseTime());
    }

    public function testSendTimeSetterAndGetter(): void
    {
        $chance = new Chance();
        $sendTime = new \DateTimeImmutable();

        $chance->setSendTime($sendTime);

        $this->assertSame($sendTime, $chance->getSendTime());
    }

    public function testRemarkSetterAndGetter(): void
    {
        $chance = new Chance();
        $remark = 'test remark';

        $chance->setRemark($remark);

        $this->assertEquals($remark, $chance->getRemark());
    }

    public function testValidSetterAndGetter(): void
    {
        $chance = new Chance();

        $chance->setValid(true);

        $this->assertTrue($chance->getValid());
    }

    public function testStatusSetterAndGetter(): void
    {
        $chance = new Chance();

        $chance->setStatus(ChanceStatusEnum::SENT);

        $this->assertEquals(ChanceStatusEnum::SENT, $chance->getStatus());
    }

    public function testActivitySetterAndGetter(): void
    {
        $chance = new Chance();
        /*
         * 使用具体类 Activity 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Chance与Activity的关联关系设置
         * 2) 使用合理性：Activity是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Activity没有对应的接口
         */
        $activity = $this->createMock(Activity::class);

        $chance->setActivity($activity);

        $this->assertSame($activity, $chance->getActivity());
    }

    public function testUserSetterAndGetter(): void
    {
        $chance = new Chance();
        $user = $this->createMock(UserInterface::class);

        $chance->setUser($user);

        $this->assertSame($user, $chance->getUser());
    }

    public function testPoolSetterAndGetter(): void
    {
        $chance = new Chance();
        /*
         * 使用具体类 Pool 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Chance与Pool的关联关系设置
         * 2) 使用合理性：Pool是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Pool没有对应的接口
         */
        $pool = $this->createMock(Pool::class);

        $chance->setPool($pool);

        $this->assertSame($pool, $chance->getPool());
    }

    public function testPrizeSetterAndGetter(): void
    {
        $chance = new Chance();
        /*
         * 使用具体类 Prize 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Chance与Prize的关联关系设置
         * 2) 使用合理性：Prize是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Prize没有对应的接口
         */
        $prize = $this->createMock(Prize::class);

        $chance->setPrize($prize);

        $this->assertSame($prize, $chance->getPrize());
    }

    public function testConsigneeSetterAndGetter(): void
    {
        $chance = new Chance();
        /*
         * 使用具体类 Consignee 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Chance与Consignee的关联关系设置
         * 2) 使用合理性：Consignee是Entity类，测试仅需要验证关联设置，不需要具体实现
         * 3) 替代方案：暂无更好方案，Consignee没有对应的接口
         */
        $consignee = $this->createMock(Consignee::class);

        $chance->setConsignee($consignee);

        $this->assertSame($consignee, $chance->getConsignee());
    }

    public function testStocksAddAndRemove(): void
    {
        $chance = new Chance();
        /*
         * 使用具体类 Stock 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Chance与Stock的集合关联关系
         * 2) 使用合理性：Stock是Entity类，测试需要模拟setChance/getChance方法
         * 3) 替代方案：暂无更好方案，Stock没有对应的接口
         */
        $stock = $this->createMock(Stock::class);

        // 模拟 Stock 的 setChance 方法
        $stock->expects($this->exactly(2))
            ->method('setChance')
            ->willReturnCallback(function ($argument) use ($chance) {
                $this->assertTrue($argument === $chance || null === $argument);
            })
        ;

        $chance->addStock($stock);

        $this->assertTrue($chance->getStocks()->contains($stock));

        // 测试移除
        $stock->expects($this->once())
            ->method('getChance')
            ->willReturn($chance)
        ;

        $chance->removeStock($stock);

        $this->assertFalse($chance->getStocks()->contains($stock));
    }

    public function testStocksAddDuplicateDoesNotDuplicate(): void
    {
        $chance = new Chance();
        /*
         * 使用具体类 Stock 创建Mock对象
         * 1) 必须使用具体类的原因：测试需要验证Chance与Stock的集合去重逻辑
         * 2) 使用合理性：Stock是Entity类，测试需要模拟setChance方法
         * 3) 替代方案：暂无更好方案，Stock没有对应的接口
         */
        $stock = $this->createMock(Stock::class);

        $stock->expects($this->once())
            ->method('setChance')
            ->with($chance)
        ;

        $chance->addStock($stock);
        $chance->addStock($stock); // 添加相同的库存

        $this->assertCount(1, $chance->getStocks());
    }

    public function testSendResultSetterAndGetter(): void
    {
        $chance = new Chance();
        $sendResult = ['status' => 'sent', 'timestamp' => time()];

        $chance->setSendResult($sendResult);

        $this->assertEquals($sendResult, $chance->getSendResult());
    }

    public function testPoolContextSetterAndGetter(): void
    {
        $chance = new Chance();
        $poolContext = ['pool_id' => 1, 'selected_at' => time()];

        $chance->setPoolContext($poolContext);

        $this->assertEquals($poolContext, $chance->getPoolContext());
    }

    public function testProbabilityContextSetterAndGetter(): void
    {
        $chance = new Chance();
        $probabilityContext = [
            ['id' => 1, 'name' => 'Prize 1', 'rate' => 50],
            ['id' => 2, 'name' => 'Prize 2', 'rate' => 30],
        ];

        $chance->setProbabilityContext($probabilityContext);

        $this->assertEquals($probabilityContext, $chance->getProbabilityContext());
    }

    public function testReviewTimeSetterAndGetter(): void
    {
        $chance = new Chance();
        $reviewTime = '2025-01-19 10:00:00';

        $chance->setReviewTime($reviewTime);

        $this->assertEquals($reviewTime, $chance->getReviewTime());
    }

    public function testReviewUserSetterAndGetter(): void
    {
        $chance = new Chance();
        $reviewUser = $this->createMock(UserInterface::class);

        $chance->setReviewUser($reviewUser);

        $this->assertSame($reviewUser, $chance->getReviewUser());
    }

    public function testLockVersionSetterAndGetter(): void
    {
        $chance = new Chance();
        $lockVersion = 5;

        $chance->setLockVersion($lockVersion);

        $this->assertEquals($lockVersion, $chance->getLockVersion());
    }

    public function testIpFieldsSettersAndGetters(): void
    {
        $chance = new Chance();
        $createIp = '192.168.1.1';
        $updateIp = '192.168.1.2';

        $chance->setCreatedFromIp($createIp);
        $chance->setUpdatedFromIp($updateIp);

        $this->assertEquals($createIp, $chance->getCreatedFromIp());
        $this->assertEquals($updateIp, $chance->getUpdatedFromIp());
    }

    public function testToStringWithNullOrZeroIdReturnsEmptyString(): void
    {
        $chance = new Chance();

        $this->assertEquals('', (string) $chance);
    }

    public function testToStringWithValidIdReturnsFormattedString(): void
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

    /**
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', '测试抽奖机会'];
        yield 'startTime' => ['startTime', new \DateTimeImmutable()];
        yield 'expireTime' => ['expireTime', (new \DateTimeImmutable())->add(new \DateInterval('P7D'))];
        yield 'useTime' => ['useTime', new \DateTimeImmutable()];
        yield 'sendTime' => ['sendTime', new \DateTimeImmutable()];
        yield 'remark' => ['remark', '测试备注'];
        yield 'valid' => ['valid', true];
        yield 'status' => ['status', ChanceStatusEnum::SENT];
        yield 'sendResult' => ['sendResult', ['status' => 'sent', 'timestamp' => time()]];
        yield 'poolContext' => ['poolContext', ['pool_id' => 1, 'selected_at' => time()]];
        yield 'probabilityContext' => ['probabilityContext', ['calculated_probability' => 0.15, 'factors' => []]];
        yield 'reviewTime' => ['reviewTime', '2025-01-19 10:00:00'];
        yield 'lockVersion' => ['lockVersion', 5];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }
}
