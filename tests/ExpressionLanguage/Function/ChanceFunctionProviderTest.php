<?php

namespace LotteryBundle\Tests\ExpressionLanguage\Function;

use LotteryBundle\ExpressionLanguage\Function\ChanceFunctionProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ChanceFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class ChanceFunctionProviderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testGetFunctionsReturnsExpressionFunctions(): void
    {
        $provider = self::getService(ChanceFunctionProvider::class);
        $functions = $provider->getFunctions();

        $this->assertIsArray($functions);
        $this->assertCount(2, $functions);
        $this->assertContainsOnlyInstancesOf('\Symfony\Component\ExpressionLanguage\ExpressionFunction', $functions);
    }

    public function testGiveLotteryChannel(): void
    {
        // 使用反射测试方法存在性和特征
        $reflection = new \ReflectionMethod(ChanceFunctionProvider::class, 'giveLotteryChannel');

        // 验证方法基本属性
        $this->assertEquals('giveLotteryChannel', $reflection->getName());
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(4, $reflection->getNumberOfParameters());

        // 验证参数
        $parameters = $reflection->getParameters();
        $this->assertCount(4, $parameters);
        $this->assertEquals('values', $parameters[0]->getName());
        $this->assertEquals('user', $parameters[1]->getName());
        $this->assertEquals('activity', $parameters[2]->getName());
        $this->assertEquals('expireTime', $parameters[3]->getName());

        // 验证返回类型
        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('bool', $returnType->__toString());
    }

    public function testGetLotteryValidChannelCount(): void
    {
        // 使用反射测试方法存在性和特征
        $reflection = new \ReflectionMethod(ChanceFunctionProvider::class, 'getLotteryValidChannelCount');

        // 验证方法基本属性
        $this->assertEquals('getLotteryValidChannelCount', $reflection->getName());
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(3, $reflection->getNumberOfParameters());

        // 验证参数
        $parameters = $reflection->getParameters();
        $this->assertCount(3, $parameters);
        $this->assertEquals('values', $parameters[0]->getName());
        $this->assertEquals('user', $parameters[1]->getName());
        $this->assertEquals('activity', $parameters[2]->getName());

        // 验证返回类型
        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals('int', $returnType->__toString());
    }
}
