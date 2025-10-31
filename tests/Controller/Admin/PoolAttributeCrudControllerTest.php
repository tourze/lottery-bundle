<?php

namespace LotteryBundle\Tests\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use LotteryBundle\Controller\Admin\PoolAttributeCrudController;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\PoolAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(PoolAttributeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PoolAttributeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<PoolAttribute>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @var AbstractCrudController<PoolAttribute> */
        return self::getContainer()->get(PoolAttributeCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '属性名称' => ['属性名称'];
        yield '属性值' => ['属性值'];
        yield '所属奖池' => ['所属奖池'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'value' => ['value'];
        yield 'pool' => ['pool'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'value' => ['value'];
        yield 'pool' => ['pool'];
    }

    public function testUnauthorizedAccessDenied(): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(AccessDeniedException::class);
        $client->request('GET', '/admin/lottery/pool-attribute');
    }

    public function testControllerIsRegistered(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $client->request('GET', '/admin/lottery/pool-attribute');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());
    }

    public function testIndexPageDisplaysCorrectly(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('Test Pool');
        $pool->setValid(true);
        $em->persist($pool);

        // Create a pool attribute
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('Test Attribute');
        $poolAttribute->setValue('Test Value');
        $poolAttribute->setPool($pool);
        $em->persist($poolAttribute);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool-attribute');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('奖池属性管理', $contentHeader->text(), 'Expected content header text');

        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count(), 'Expected table element');
        $this->assertStringContainsString('Test Attribute', $table->text(), 'Expected table content');
    }

    public function testNewFormIsAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $client->request('GET', '/admin/lottery/pool-attribute?crudAction=new');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $form = $crawler->filter('form');
        $this->assertGreaterThan(0, $form->count(), 'Expected form element');

        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('奖池属性', $contentHeader->text(), 'Expected content header text');
    }

    public function testEditFormIsAccessible(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('Edit Pool');
        $pool->setValid(true);
        $em->persist($pool);

        // Create a pool attribute
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('Edit Attribute');
        $poolAttribute->setValue('Edit Value');
        $poolAttribute->setPool($pool);
        $em->persist($poolAttribute);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool-attribute?crudAction=edit&entityId=' . $poolAttribute->getId());

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

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('Detail Pool');
        $pool->setValid(true);
        $em->persist($pool);

        // Create a pool attribute
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('Detail Attribute');
        $poolAttribute->setValue('Detail Value');
        $poolAttribute->setPool($pool);
        $em->persist($poolAttribute);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool-attribute?crudAction=detail&entityId=' . $poolAttribute->getId());

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $contentHeader = $crawler->filter('.content-header');
        $this->assertGreaterThan(0, $contentHeader->count(), 'Expected .content-header element');
        $this->assertStringContainsString('奖池属性', $contentHeader->text(), 'Expected content header text');
    }

    public function testEntityCrudOperations(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('CRUD Test Pool');
        $pool->setValid(true);
        $em->persist($pool);

        // Create a pool attribute
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('CRUD Test Attribute');
        $poolAttribute->setValue('CRUD Test Value');
        $poolAttribute->setRemark('CRUD Test Remark');
        $poolAttribute->setPool($pool);
        $em->persist($poolAttribute);
        $em->flush();

        $this->assertInstanceOf(PoolAttribute::class, $poolAttribute);
        $this->assertSame('CRUD Test Attribute', $poolAttribute->getName());
        $this->assertSame('CRUD Test Value', $poolAttribute->getValue());
        $this->assertSame('CRUD Test Remark', $poolAttribute->getRemark());
        $this->assertSame($pool, $poolAttribute->getPool());

        // Update the pool attribute
        $poolAttribute->setName('Updated CRUD Attribute');
        $poolAttribute->setValue('Updated CRUD Value');
        $poolAttribute->setRemark('Updated CRUD Remark');
        $em->flush();

        $em->refresh($poolAttribute);
        $this->assertSame('Updated CRUD Attribute', $poolAttribute->getName());
        $this->assertSame('Updated CRUD Value', $poolAttribute->getValue());
        $this->assertSame('Updated CRUD Remark', $poolAttribute->getRemark());
    }

    public function testTableDisplaysData(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('Table Display Pool');
        $pool->setValid(true);
        $em->persist($pool);

        // Create a pool attribute
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('Table Display Attribute');
        $poolAttribute->setValue('Table Display Value');
        $poolAttribute->setPool($pool);
        $em->persist($poolAttribute);
        $em->flush();

        $client->request('GET', '/admin/lottery/pool-attribute');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful(), 'Expected successful response but got: ' . $response->getStatusCode());

        $crawler = $client->getCrawler();
        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count(), 'Expected table element');
        $this->assertStringContainsString('Table Display Attribute', $table->text(), 'Expected table content');
    }

    public function testEntityToStringMethod(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('ToString Pool');
        $pool->setValid(true);
        $em->persist($pool);

        // Create a pool attribute
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setName('ToString Attribute');
        $poolAttribute->setValue('ToString Value');
        $poolAttribute->setPool($pool);
        $em->persist($poolAttribute);
        $em->flush();

        $expectedString = 'ToString Attribute:ToString Value';
        $this->assertSame($expectedString, (string) $poolAttribute);
    }

    public function testEntityValidation(): void
    {
        $client = self::createClientWithDatabase();
        $admin = $this->createAdminUser('admin@test.com', 'password123');
        $this->loginAsAdmin($client, 'admin@test.com', 'password123');

        $em = self::getService(EntityManagerInterface::class);

        // Create a pool first
        $pool = new Pool();
        $pool->setTitle('Validation Pool');
        $pool->setValid(true);
        $em->persist($pool);
        $em->flush();

        // Test that required fields are validated
        $poolAttribute = new PoolAttribute();
        $poolAttribute->setPool($pool);

        // Name and value are required, so we need to set them
        $poolAttribute->setName('Valid Name');
        $poolAttribute->setValue('Valid Value');

        $em->persist($poolAttribute);
        $em->flush();

        $this->assertNotNull($poolAttribute->getId());
        $this->assertSame('Valid Name', $poolAttribute->getName());
        $this->assertSame('Valid Value', $poolAttribute->getValue());
    }
}
