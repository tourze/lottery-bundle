<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\JoinLotteryParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * JoinLotteryParam 单元测试
 *
 * @internal
 */
#[CoversClass(JoinLotteryParam::class)]
final class JoinLotteryParamTest extends TestCase
{

    public function testConstructor(): void
    {
        $activityId = 123;
        $count = 5;

        $param = new JoinLotteryParam(
            activityId: $activityId,
            count: $count
        );

        $this->assertSame($activityId, $param->activityId);
        $this->assertSame($count, $param->count);
    }

    public function testConstructorWithDefaultCount(): void
    {
        $activityId = 456;

        $param = new JoinLotteryParam(activityId: $activityId);

        $this->assertSame($activityId, $param->activityId);
        $this->assertSame(1, $param->count); // 默认值
    }

    public function testImplementsRpcParamInterface(): void
    {
        $param = new JoinLotteryParam(activityId: 789);

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(JoinLotteryParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(JoinLotteryParam::class);

        $activityIdProperty = $reflection->getProperty('activityId');
        $this->assertTrue($activityIdProperty->isPublic(), 'activityId should be public');
        $this->assertTrue($activityIdProperty->isReadOnly(), 'activityId should be readonly');

        $countProperty = $reflection->getProperty('count');
        $this->assertTrue($countProperty->isPublic(), 'count should be public');
        $this->assertTrue($countProperty->isReadOnly(), 'count should be readonly');
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(JoinLotteryParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();

        // 检查 activityId 参数
        $activityIdParam = null;
        $countParam = null;

        foreach ($parameters as $param) {
            if ($param->getName() === 'activityId') {
                $activityIdParam = $param;
            } elseif ($param->getName() === 'count') {
                $countParam = $param;
            }
        }

        $this->assertNotNull($activityIdParam, 'activityId parameter should exist');
        $this->assertNotNull($countParam, 'count parameter should exist');

        // 检查 MethodParam 属性
        $activityIdMethodParamAttrs = $activityIdParam->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $this->assertNotEmpty($activityIdMethodParamAttrs, 'activityId parameter should have MethodParam attribute');

        $countMethodParamAttrs = $countParam->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $this->assertNotEmpty($countMethodParamAttrs, 'count parameter should have MethodParam attribute');
    }

    public function testMethodParamAttributeDescriptions(): void
    {
        $reflection = new \ReflectionClass(JoinLotteryParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();

        // 获取 activityId 参数
        $activityIdParam = null;
        $countParam = null;

        foreach ($parameters as $param) {
            if ($param->getName() === 'activityId') {
                $activityIdParam = $param;
            } elseif ($param->getName() === 'count') {
                $countParam = $param;
            }
        }

        $this->assertNotNull($activityIdParam, 'activityId parameter should exist');
        $this->assertNotNull($countParam, 'count parameter should exist');

        // 检查描述
        $activityIdMethodParamAttrs = $activityIdParam->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $activityIdMethodParamInstance = $activityIdMethodParamAttrs[0]->newInstance();
        $this->assertSame('活动ID', $activityIdMethodParamInstance->description);

        $countMethodParamAttrs = $countParam->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $countMethodParamInstance = $countMethodParamAttrs[0]->newInstance();
        $this->assertSame('连续抽取次数', $countMethodParamInstance->description);
    }
}
