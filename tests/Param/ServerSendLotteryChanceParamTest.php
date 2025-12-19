<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\ServerSendLotteryChanceParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * ServerSendLotteryChanceParam 单元测试
 *
 * @internal
 */
#[CoversClass(ServerSendLotteryChanceParam::class)]
final class ServerSendLotteryChanceParamTest extends TestCase
{

    public function testConstructor(): void
    {
        $activityId = 123;
        $userIdentity = 'user123';
        $title = '系统赠送';
        $startTime = '2024-01-01 00:00:00';
        $expireTime = '2024-12-31 23:59:59';

        $param = new ServerSendLotteryChanceParam(
            activityId: $activityId,
            userIdentity: $userIdentity,
            title: $title,
            startTime: $startTime,
            expireTime: $expireTime
        );

        $this->assertSame($activityId, $param->activityId);
        $this->assertSame($userIdentity, $param->userIdentity);
        $this->assertSame($title, $param->title);
        $this->assertSame($startTime, $param->startTime);
        $this->assertSame($expireTime, $param->expireTime);
    }

    public function testConstructorWithDefaultValues(): void
    {
        $activityId = 456;
        $userIdentity = 'user456';

        $param = new ServerSendLotteryChanceParam(
            activityId: $activityId,
            userIdentity: $userIdentity
        );

        $this->assertSame($activityId, $param->activityId);
        $this->assertSame($userIdentity, $param->userIdentity);
        $this->assertSame('', $param->title); // 默认值
        $this->assertSame('', $param->startTime); // 默认值
        $this->assertSame('', $param->expireTime); // 默认值
    }

    public function testImplementsRpcParamInterface(): void
    {
        $param = new ServerSendLotteryChanceParam(
            activityId: 1,
            userIdentity: 'test'
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(ServerSendLotteryChanceParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(ServerSendLotteryChanceParam::class);

        $properties = ['activityId', 'userIdentity', 'title', 'startTime', 'expireTime'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(ServerSendLotteryChanceParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();

        $expectedParams = ['activityId', 'userIdentity', 'title', 'startTime', 'expireTime'];
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
        $reflection = new \ReflectionClass(ServerSendLotteryChanceParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();

        $expectedDescriptions = [
            'activityId' => '抽奖活动ID',
            'userIdentity' => '用户唯一标志',
            'title' => '获得机会的说明信息',
            'startTime' => '有效开始时间,不传入则不限制',
            'expireTime' => '结束时间,不传入则不限制'
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
