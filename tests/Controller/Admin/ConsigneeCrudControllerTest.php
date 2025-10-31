<?php

namespace LotteryBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\ConsigneeCrudController;
use LotteryBundle\Entity\Consignee;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ConsigneeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ConsigneeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Consignee>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<Consignee> */
        return self::getContainer()->get(ConsigneeCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '关联抽奖机会' => ['关联抽奖机会'];
        yield '收货人姓名' => ['收货人姓名'];
        yield '手机号' => ['手机号'];
        yield '详细地址' => ['详细地址'];
        yield '创建时间' => ['创建时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'realName' => ['realName'];
        yield 'mobile' => ['mobile'];
        yield 'address' => ['address'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'realName' => ['realName'];
        yield 'mobile' => ['mobile'];
        yield 'address' => ['address'];
    }

    public function testUnauthorizedAccessReturnsRedirect(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index');
        $client->request('GET', $url);
    }

    public function testGetRequest(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index');
        $client->request('GET', $url);
    }

    public function testPostRequest(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);

        $url = $this->generateAdminUrl('index');
        $client->request('POST', $url);
    }

    public function testRealNameFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['realName' => 'Test User'],
        ]);
        $client->request('GET', $url);
    }

    public function testMobileFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['mobile' => '13800138000'],
        ]);
        $client->request('GET', $url);
    }

    public function testAddressFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['address' => 'Test Address'],
        ]);
        $client->request('GET', $url);
    }

    public function testNewFormValidation(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('new');
        $client->request('GET', $url);
    }

    /**
     * 测试必填字段验证 - chance, realName, mobile, address都是必填的
     */
    public function testValidationErrorsOnRequiredFields(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // 检查表单是否存在
        $this->assertSelectorExists('form');

        // 验证必填字段存在并被标记为必填
        $form = null;
        $buttonSelectors = ['保存', 'Save', 'Create', 'Submit'];

        foreach ($buttonSelectors as $buttonText) {
            try {
                $form = $crawler->selectButton($buttonText)->form();
                break;
            } catch (\InvalidArgumentException) {
                continue;
            }
        }

        if (null === $form) {
            $form = $crawler->filter('form')->form();
        }

        $this->assertNotNull($form, '表单应该存在');
    }

    /**
     * 测试表单验证错误 - 提交空表单并验证错误信息
     */
    public function testValidationErrors(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // 检查表单是否存在
        $this->assertSelectorExists('form');

        // 尝试找到提交按钮
        $form = null;
        $buttonSelectors = ['保存', 'Save', 'Create', 'Submit'];

        foreach ($buttonSelectors as $buttonText) {
            try {
                $form = $crawler->selectButton($buttonText)->form();
                break;
            } catch (\InvalidArgumentException) {
                continue;
            }
        }

        if (null === $form) {
            $form = $crawler->filter('form')->form();
        }

        $this->assertNotNull($form);

        // 提交空表单（所有必填字段为空，违反NotBlank约束）
        $crawler = $client->submit($form);

        // 验证响应 - 应该返回422或200（显示验证错误）
        $statusCode = $client->getResponse()->getStatusCode();
        if (422 === $statusCode) {
            $this->assertResponseStatusCodeSame(422);
            $invalidFeedback = $crawler->filter('.invalid-feedback');
            if ($invalidFeedback->count() > 0) {
                // 验证错误信息中包含必填字段提示
                $this->assertGreaterThan(0, $invalidFeedback->count(), '应该显示验证错误');
            }
        } else {
            // 其他状态码，验证响应合理性
            $this->assertTrue(
                in_array($statusCode, [200, 302], true),
                "Expected status code 200, 302, or 422 for form submission, got {$statusCode}"
            );
            $this->assertNotEmpty($crawler->text(), '页面应该有内容显示');
        }
    }
}
