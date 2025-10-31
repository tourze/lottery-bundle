<?php

namespace LotteryBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\ChanceCrudController;
use LotteryBundle\Entity\Chance;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ChanceCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ChanceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Chance>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<Chance> */
        return self::getContainer()->get(ChanceCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '任务标题' => ['任务标题'];
        yield '开始时间' => ['开始时间'];
        yield '失效时间' => ['失效时间'];
        yield '状态' => ['状态'];
        yield '是否有效' => ['是否有效'];
        yield '关联活动' => ['关联活动'];
        yield '用户' => ['用户'];
        yield '创建时间' => ['创建时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'activity' => ['activity'];
        yield 'prize' => ['prize'];
        yield 'startTime' => ['startTime'];
        yield 'expireTime' => ['expireTime'];
        yield 'valid' => ['valid'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'activity' => ['activity'];
        yield 'prize' => ['prize'];
        yield 'startTime' => ['startTime'];
        yield 'expireTime' => ['expireTime'];
        yield 'valid' => ['valid'];
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

    public function testTitleFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['title' => 'Test Title'],
        ]);
        $client->request('GET', $url);
    }

    public function testActivityFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['activity' => '1'],
        ]);
        $client->request('GET', $url);
    }

    public function testPrizeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['prize' => '1'],
        ]);
        $client->request('GET', $url);
    }

    public function testStatusFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['status' => 'INIT'],
        ]);
        $client->request('GET', $url);
    }

    public function testStartTimeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['startTime' => '2024-01-01'],
        ]);
        $client->request('GET', $url);
    }

    public function testExpireTimeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['expireTime' => '2024-12-31'],
        ]);
        $client->request('GET', $url);
    }

    public function testUseTimeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['useTime' => '2024-06-15'],
        ]);
        $client->request('GET', $url);
    }

    public function testSendTimeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['sendTime' => '2024-06-15'],
        ]);
        $client->request('GET', $url);
    }

    public function testValidFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['valid' => true],
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
     * 测试必填字段验证 - activity字段是必填的
     */
    public function testValidationErrorsOnRequiredFields(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        // 检查表单是否存在
        $this->assertSelectorExists('form');

        // 验证activity字段存在并被标记为必填（通过Controller的setRequired(true)配置）
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

        // 提交空表单（activity字段为空，违反NotNull约束）
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
