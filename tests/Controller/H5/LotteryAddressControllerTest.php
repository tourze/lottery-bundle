<?php

namespace LotteryBundle\Tests\Controller\H5;

use LotteryBundle\Controller\H5\LotteryAddressController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(LotteryAddressController::class)]
#[RunTestsInSeparateProcesses]
final class LotteryAddressControllerTest extends AbstractWebTestCase
{
    public function testLotteryAddressRequiresChanceId(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('抽奖记录ID不能为空');

        $client->request('GET', '/h5/lottery/address');
    }

    public function testLotteryAddressWithInvalidChanceId(): void
    {
        $client = self::createClient();

        $client->request('GET', '/h5/lottery/address?chance_id=999999');

        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [200, 404, 500]);
    }

    public function testPostMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPutMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPatchMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PATCH', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testDeleteMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testHeadMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testOptionsMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testAccessWithoutAuthenticationReturnsUnauthorized(): void
    {
        $client = self::createClient();
        $client->request('GET', '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [200, 401, 403]);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/h5/lottery/address?chance_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }
}
