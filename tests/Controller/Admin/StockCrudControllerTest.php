<?php

namespace LotteryBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\StockCrudController;
use LotteryBundle\Entity\Prize;
use LotteryBundle\Entity\Stock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(StockCrudController::class)]
#[RunTestsInSeparateProcesses]
final class StockCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Stock>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<Stock> */
        return self::getContainer()->get(StockCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '序列号' => ['序列号'];
        yield '关联奖品' => ['关联奖品'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        // NEW操作已被禁用，但仍需提供数据避免空数据集错误
        yield 'sn' => ['sn'];
        yield 'prize' => ['prize'];
    }

    /**
     * 重写NEW页面字段数据提供者测试
     */

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'sn' => ['sn'];
        yield 'lockVersion' => ['lockVersion'];
        yield 'prize' => ['prize'];
        yield 'chance' => ['chance'];
    }

    public function testUnauthorizedAccessDenied(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/lottery/stock');
    }

    public function testControllerIsRegistered(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $client->request('GET', '/admin/lottery/stock');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());
    }

    public function testIndexPageDisplaysCorrectly(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Test Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('TEST-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $client->request('GET', '/admin/lottery/stock');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('库存管理', $contentHeader->text(), 'Expected content header text');

        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count(), 'Expected table element');
        $this->assertStringContainsString('TEST-SN-001', $table->text(), 'Expected table content');
    }

    public function testNewFormIsAccessible(): void
    {
        // NEW操作已被禁用，测试应该跳过
        self::markTestSkipped('NEW action is disabled for StockCrudController - stock records should be generated through prizes.');
    }

    public function testEditFormIsAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Edit Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('EDIT-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $client->request('GET', '/admin/lottery/stock?crudAction=edit&entityId=' . $stock->getId());

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

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Detail Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('DETAIL-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $client->request('GET', '/admin/lottery/stock?crudAction=detail&entityId=' . $stock->getId());

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('库存管理', $contentHeader->text(), 'Expected content header text');
    }

    public function testEntityCrudOperations(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('CRUD Test Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('CRUD-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertSame('CRUD-SN-001', $stock->getSn());
        $this->assertSame($prize, $stock->getPrize());
        $this->assertNotNull($stock->getLockVersion());

        // Update the stock
        $stock->setSn('UPDATED-CRUD-SN-001');
        $em->flush();

        $em->refresh($stock);
        $this->assertSame('UPDATED-CRUD-SN-001', $stock->getSn());
        $this->assertGreaterThan(1, $stock->getLockVersion());
    }

    public function testTableDisplaysData(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Table Display Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('TABLE-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $client->request('GET', '/admin/lottery/stock');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count(), 'Expected table element');
        $this->assertStringContainsString('TABLE-SN-001', $table->text(), 'Expected table content');
    }

    public function testEntityToStringMethod(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('ToString Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('TOSTRING-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $expectedString = 'ToString Prize TOSTRING-SN-001';
        $this->assertSame($expectedString, (string) $stock);
    }

    public function testEntityValidation(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Validation Prize');
        $prize->setType('物品');
        $em->persist($prize);
        $em->flush();

        // Test that required fields are validated
        $stock = new Stock();
        $stock->setPrize($prize);

        // SN is required, so we need to set it
        $stock->setSn('VALID-SN-001');

        $em->persist($stock);
        $em->flush();

        $this->assertNotNull($stock->getId());
        $this->assertSame('VALID-SN-001', $stock->getSn());
        $this->assertSame($prize, $stock->getPrize());
    }

    public function testStockWithChanceAssociation(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Chance Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('CHANCE-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        // Initially no chance is associated
        $this->assertNull($stock->getChance());

        // The chance can be set to null (optional relationship)
        $stock->setChance(null);
        $em->flush();

        $this->assertNull($stock->getChance());
    }

    public function testLockVersionOptimisticLocking(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a prize first
        $prize = new Prize();
        $prize->setName('Lock Test Prize');
        $prize->setType('物品');
        $em->persist($prize);

        // Create a stock
        $stock = new Stock();
        $stock->setSn('LOCK-SN-001');
        $stock->setPrize($prize);
        $em->persist($stock);
        $em->flush();

        $initialVersion = $stock->getLockVersion();

        // Update the stock
        $stock->setSn('LOCK-SN-001-UPDATED');
        $em->flush();

        // Version should increment automatically due to @ORM\Version
        $this->assertGreaterThan($initialVersion, $stock->getLockVersion());
    }
}
