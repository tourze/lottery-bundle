<?php

namespace LotteryBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\ActivityCrudController;
use LotteryBundle\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ActivityCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ActivityCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Activity>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<Activity> */
        return self::getContainer()->get(ActivityCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '活动标题' => ['活动标题'];
        yield '开始时间' => ['开始时间'];
        yield '结束时间' => ['结束时间'];
        yield '头图' => ['头图'];
        yield '是否有效' => ['是否有效'];
        yield '创建时间' => ['创建时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield '活动标题' => ['title'];
        yield '活动规则' => ['textRule'];
        yield '开始时间' => ['startTime'];
        yield '结束时间' => ['endTime'];
        yield '最后兑奖时间' => ['lastRedeemTime'];
        yield '头图' => ['headPhoto'];
        yield '无机会提示文本' => ['noChanceText'];
        yield '分享路径' => ['sharePath'];
        yield '分享标题' => ['shareTitle'];
        yield '分享图片' => ['sharePicture'];
        yield '关联奖池' => ['pools'];
        yield '是否有效' => ['valid'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield '活动标题' => ['title'];
        yield '活动规则' => ['textRule'];
        yield '开始时间' => ['startTime'];
        yield '结束时间' => ['endTime'];
        yield '最后兑奖时间' => ['lastRedeemTime'];
        yield '头图' => ['headPhoto'];
        yield '无机会提示文本' => ['noChanceText'];
        yield '分享路径' => ['sharePath'];
        yield '分享标题' => ['shareTitle'];
        yield '分享图片' => ['sharePicture'];
        yield '关联奖池' => ['pools'];
        yield '是否有效' => ['valid'];
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
            'filters' => ['title' => 'Test Activity'],
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

    public function testEndTimeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['endTime' => '2024-12-31'],
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
}
