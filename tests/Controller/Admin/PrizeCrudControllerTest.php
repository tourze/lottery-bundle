<?php

namespace LotteryBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\PrizeCrudController;
use LotteryBundle\Entity\Prize;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(PrizeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PrizeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Prize>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<Prize> */
        return self::getContainer()->get(PrizeCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '奖品名称' => ['奖品名称'];
        yield '类型' => ['类型'];
        yield '总数量' => ['总数量'];
        yield '概率数' => ['概率数'];
        yield '主图' => ['主图'];
        yield '所属奖池' => ['所属奖池'];
        yield '是否有效' => ['是否有效'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'type' => ['type'];
        yield 'pool' => ['pool'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'type' => ['type'];
        yield 'pool' => ['pool'];
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

    public function testNameFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['name' => 'Test Prize'],
        ]);
        $client->request('GET', $url);
    }

    public function testTypeFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['type' => 'cash'],
        ]);
        $client->request('GET', $url);
    }

    public function testPoolFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['pool' => '1'],
        ]);
        $client->request('GET', $url);
    }

    public function testQuantityFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['quantity' => '100'],
        ]);
        $client->request('GET', $url);
    }

    public function testProbabilityFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['probability' => '50'],
        ]);
        $client->request('GET', $url);
    }

    public function testIsDefaultFilter(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('index', [
            'filters' => ['isDefault' => true],
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

    public function testBackToPoolAction(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);

        $url = $this->generateAdminUrl('backToPool', [
            'entityId' => '1',
        ]);
        $client->request('GET', $url);
    }
}
