<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\SaveOrUpdateLotteryConsigneeParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * SaveOrUpdateLotteryConsigneeParam 单元测试
 *
 * @internal
 */
#[CoversClass(SaveOrUpdateLotteryConsigneeParam::class)]
final class SaveOrUpdateLotteryConsigneeParamTest extends TestCase
{

    public function testConstructor(): void
    {
        $chanceId = 123;
        $realName = '张三';
        $mobile = '13800138000';
        $address = '北京市朝阳区';
        $consigneeId = 456;

        $param = new SaveOrUpdateLotteryConsigneeParam(
            chanceId: $chanceId,
            realName: $realName,
            mobile: $mobile,
            address: $address,
            consigneeId: $consigneeId
        );

        $this->assertSame($chanceId, $param->chanceId);
        $this->assertSame($realName, $param->realName);
        $this->assertSame($mobile, $param->mobile);
        $this->assertSame($address, $param->address);
        $this->assertSame($consigneeId, $param->consigneeId);
    }

    public function testConstructorWithDefaultConsigneeId(): void
    {
        $chanceId = 789;
        $realName = '李四';
        $mobile = '13900139000';
        $address = '上海市浦东新区';

        $param = new SaveOrUpdateLotteryConsigneeParam(
            chanceId: $chanceId,
            realName: $realName,
            mobile: $mobile,
            address: $address
        );

        $this->assertSame($chanceId, $param->chanceId);
        $this->assertSame($realName, $param->realName);
        $this->assertSame($mobile, $param->mobile);
        $this->assertSame($address, $param->address);
        $this->assertSame(0, $param->consigneeId); // 默认值
    }

    public function testImplementsRpcParamInterface(): void
    {
        $param = new SaveOrUpdateLotteryConsigneeParam(
            chanceId: 1,
            realName: 'test',
            mobile: 'test',
            address: 'test'
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(SaveOrUpdateLotteryConsigneeParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(SaveOrUpdateLotteryConsigneeParam::class);

        $chanceIdProperty = $reflection->getProperty('chanceId');
        $this->assertTrue($chanceIdProperty->isPublic(), 'chanceId should be public');
        $this->assertTrue($chanceIdProperty->isReadOnly(), 'chanceId should be readonly');

        $realNameProperty = $reflection->getProperty('realName');
        $this->assertTrue($realNameProperty->isPublic(), 'realName should be public');
        $this->assertTrue($realNameProperty->isReadOnly(), 'realName should be readonly');

        $mobileProperty = $reflection->getProperty('mobile');
        $this->assertTrue($mobileProperty->isPublic(), 'mobile should be public');
        $this->assertTrue($mobileProperty->isReadOnly(), 'mobile should be readonly');

        $addressProperty = $reflection->getProperty('address');
        $this->assertTrue($addressProperty->isPublic(), 'address should be public');
        $this->assertTrue($addressProperty->isReadOnly(), 'address should be readonly');

        $consigneeIdProperty = $reflection->getProperty('consigneeId');
        $this->assertTrue($consigneeIdProperty->isPublic(), 'consigneeId should be public');
        $this->assertTrue($consigneeIdProperty->isReadOnly(), 'consigneeId should be readonly');
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(SaveOrUpdateLotteryConsigneeParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();

        $expectedParams = ['chanceId', 'realName', 'mobile', 'address', 'consigneeId'];
        $foundParams = [];

        foreach ($parameters as $param) {
            if (in_array($param->getName(), $expectedParams, true)) {
                $foundParams[$param->getName()] = $param;
            }
        }

        foreach ($expectedParams as $paramName) {
            $this->assertArrayHasKey($paramName, $foundParams, "{$paramName} parameter should exist");

            $methodParamAttrs = $foundParams[$paramName]->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($methodParamAttrs, "{$paramName} parameter should have MethodParam attribute");
        }
    }

    public function testMethodParamAttributeDescriptions(): void
    {
        $reflection = new \ReflectionClass(SaveOrUpdateLotteryConsigneeParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();

        $expectedDescriptions = [
            'chanceId' => '抽奖机会id',
            'realName' => '姓名',
            'mobile' => '手机号',
            'address' => '地址',
            'consigneeId' => '抽奖地址ID'
        ];

        $foundParams = [];
        foreach ($parameters as $param) {
            $foundParams[$param->getName()] = $param;
        }

        foreach ($expectedDescriptions as $paramName => $expectedDescription) {
            $this->assertArrayHasKey($paramName, $foundParams, "{$paramName} parameter should exist");

            $methodParamAttrs = $foundParams[$paramName]->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($methodParamAttrs, "{$paramName} parameter should have MethodParam attribute");

            $methodParamInstance = $methodParamAttrs[0]->newInstance();
            $this->assertSame($expectedDescription, $methodParamInstance->description);
        }
    }
}
