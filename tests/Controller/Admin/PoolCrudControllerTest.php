<?php

namespace LotteryBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\PoolCrudController;
use LotteryBundle\Entity\Pool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(PoolCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PoolCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Pool>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<Pool> */
        return self::getContainer()->get(PoolCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '奖池名称' => ['奖池名称'];
        yield '奖品数量' => ['奖品数量'];
        yield '关联活动数' => ['关联活动数'];
        yield '是否有效' => ['是否有效'];
        yield '创建时间' => ['创建时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'activities' => ['activities'];
        yield 'valid' => ['valid'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'activities' => ['activities'];
        yield 'valid' => ['valid'];
    }

    public function testUnauthorizedAccessDenied(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/lottery/pool');
    }

    public function testControllerIsRegistered(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $client->request('GET', '/admin/lottery/pool');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());
    }

    public function testIndexPageDisplaysCorrectly(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);
        $pool = new Pool();
        $pool->setTitle('Test Pool');
        $pool->setValid(true);
        $em->persist($pool);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('奖池管理', $contentHeader->text(), 'Expected content header text');

        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count(), 'Expected table element');
        $this->assertStringContainsString('Test Pool', $table->text(), 'Expected table content');
    }

    public function testNewFormIsAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $client->request('GET', '/admin/lottery/pool?crudAction=new');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $form = $crawler->filter('form');
        $this->assertGreaterThan(0, $form->count(), 'Expected form element');

        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('奖池', $contentHeader->text(), 'Expected content header text');
    }

    public function testEditFormIsAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);
        $pool = new Pool();
        $pool->setTitle('Edit Pool');
        $pool->setValid(true);
        $em->persist($pool);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool?crudAction=edit&entityId=' . $pool->getId());

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $form = $crawler->filter('form');
        $this->assertGreaterThan(0, $form->count(), 'Expected form element');
    }

    public function testDetailPageIsAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);
        $pool = new Pool();
        $pool->setTitle('Detail Pool');
        $pool->setValid(true);
        $em->persist($pool);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool?crudAction=detail&entityId=' . $pool->getId());

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('奖池', $contentHeader->text(), 'Expected content header text');
    }

    public function testEntityCrudOperations(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        $pool = new Pool();
        $pool->setTitle('CRUD Test Pool');
        $pool->setValid(false);
        $em->persist($pool);
        $em->flush();

        $this->assertInstanceOf(Pool::class, $pool);
        $this->assertSame('CRUD Test Pool', $pool->getTitle());
        $this->assertFalse($pool->isValid());

        $pool->setTitle('Updated CRUD Pool');
        $pool->setValid(true);
        $em->flush();

        $em->refresh($pool);
        $this->assertSame('Updated CRUD Pool', $pool->getTitle());
        $this->assertTrue($pool->isValid());
    }

    public function testTableDisplaysData(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        $pool = new Pool();
        $pool->setTitle('Table Display Pool');
        $pool->setValid(true);
        $em->persist($pool);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count(), 'Expected table element');
        $this->assertStringContainsString('Table Display Pool', $table->text(), 'Expected table content');
    }

    public function testManagePrizesAction(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);
        $pool = new Pool();
        $pool->setTitle('Prize Management Pool');
        $pool->setValid(true);
        $em->persist($pool);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool/' . $pool->getId() . '/prizes');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection(), 'Expected redirect response but got: ' . $response->getStatusCode());

        $redirectUrl = $response->headers->get('Location');
        $this->assertIsString($redirectUrl);
        $this->assertStringContainsString('/admin/lottery/prize', $redirectUrl);
        $this->assertStringContainsString('filters%5Bpool%5D', $redirectUrl);
        $this->assertStringContainsString((string) $pool->getId(), $redirectUrl);
    }
}
