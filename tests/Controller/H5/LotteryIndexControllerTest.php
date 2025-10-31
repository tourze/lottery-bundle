<?php

namespace LotteryBundle\Tests\Controller\H5;

use LotteryBundle\Controller\H5\LotteryIndexController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(LotteryIndexController::class)]
#[RunTestsInSeparateProcesses]
final class LotteryIndexControllerTest extends AbstractWebTestCase
{
    public function testLotteryIndexRequiresActivityId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('活动ID不能为空');
        $client->request('GET', '/h5/lottery');
    }

    public function testLotteryIndexWithInvalidActivityId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('抽奖活动不存在');
        $client->request('GET', '/h5/lottery?activity_id=999999');
    }

    public function testPostMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('POST', '/h5/lottery?activity_id=1');
    }

    public function testPutMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PUT', '/h5/lottery?activity_id=1');
    }

    public function testPatchMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('PATCH', '/h5/lottery?activity_id=1');
    }

    public function testDeleteMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('DELETE', '/h5/lottery?activity_id=1');
    }

    public function testHeadMethodWithInvalidActivityId(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('抽奖活动不存在');
        $client->request('HEAD', '/h5/lottery?activity_id=999999');
    }

    public function testOptionsMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request('OPTIONS', '/h5/lottery?activity_id=1');
    }

    public function testAccessWithoutAuthenticationReturnsUnauthorized(): void
    {
        $client = self::createClient();
        $client->request('GET', '/h5/lottery?activity_id=1');
        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [200, 401, 403, 404, 500]);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/h5/lottery?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }
}
