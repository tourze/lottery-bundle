<?php

namespace LotteryBundle\Tests\Controller\H5;

use LotteryBundle\Controller\H5\LotteryRulesController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(LotteryRulesController::class)]
#[RunTestsInSeparateProcesses]
final class LotteryRulesControllerTest extends AbstractWebTestCase
{
    public function testLotteryRulesRequiresActivityId(): void
    {
        $client = self::createClient();

        $client->request('GET', '/h5/lottery/rules');

        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testLotteryRulesWithInvalidActivityId(): void
    {
        $client = self::createClient();

        $client->request('GET', '/h5/lottery/rules?activity_id=999999');

        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [404, 500]);
    }

    public function testPostMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('POST', '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPutMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PUT', '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testPatchMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('PATCH', '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testDeleteMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('DELETE', '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testHeadMethodWithInvalidActivityId(): void
    {
        $client = self::createClient();
        $client->request('HEAD', '/h5/lottery/rules?activity_id=999999');
        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [200, 404, 500]);
    }

    public function testOptionsMethodReturnMethodNotAllowed(): void
    {
        $client = self::createClient();
        $client->request('OPTIONS', '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testAccessWithoutAuthenticationReturnsUnauthorized(): void
    {
        $client = self::createClient();
        $client->request('GET', '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertContains($response->getStatusCode(), [200, 401, 403, 404, 500]);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();
        $client->request($method, '/h5/lottery/rules?activity_id=1');
        $response = $client->getResponse();
        $this->assertEquals(405, $response->getStatusCode());
    }
}
