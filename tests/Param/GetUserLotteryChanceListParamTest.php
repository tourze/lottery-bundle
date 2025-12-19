<?php

declare(strict_types=1);

namespace LotteryBundle\Tests\Param;

use LotteryBundle\Param\GetUserLotteryChanceListParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetUserLotteryChanceListParam 单元测试
 *
 * @internal
 */
#[CoversClass(GetUserLotteryChanceListParam::class)]
final class GetUserLotteryChanceListParamTest extends TestCase
{

    public function testConstructor(): void
    {
        $activityId = 123;

        $param = new GetUserLotteryChanceListParam(activityId: $activityId);

        $this->assertSame($activityId, $param->activityId);
    }

    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetUserLotteryChanceListParam(activityId: 456);

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetUserLotteryChanceListParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(GetUserLotteryChanceListParam::class);

        $activityIdProperty = $reflection->getProperty('activityId');
        $this->assertTrue($activityIdProperty->isPublic(), 'activityId should be public');
        $this->assertTrue($activityIdProperty->isReadOnly(), 'activityId should be readonly');
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(GetUserLotteryChanceListParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $activityIdParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'activityId') {
                $activityIdParam = $param;
                break;
            }
        }

        $this->assertNotNull($activityIdParam, 'activityId parameter should exist');

        $methodParamAttrs = $activityIdParam->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $this->assertNotEmpty($methodParamAttrs, 'activityId parameter should have MethodParam attribute');
    }

    public function testMethodParamAttributeDescription(): void
    {
        $reflection = new \ReflectionClass(GetUserLotteryChanceListParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $activityIdParam = null;
        foreach ($parameters as $param) {
            if ($param->getName() === 'activityId') {
                $activityIdParam = $param;
                break;
            }
        }

        $this->assertNotNull($activityIdParam, 'activityId parameter should exist');

        $methodParamAttrs = $activityIdParam->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
        $this->assertNotEmpty($methodParamAttrs, 'activityId parameter should have MethodParam attribute');

        $methodParamInstance = $methodParamAttrs[0]->newInstance();
        $this->assertSame('活动ID', $methodParamInstance->description);
    }
}
