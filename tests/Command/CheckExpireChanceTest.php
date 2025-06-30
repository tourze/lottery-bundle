<?php

namespace LotteryBundle\Tests\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use LotteryBundle\Command\CheckExpireChance;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\AfterChanceExpireEvent;
use LotteryBundle\Repository\ChanceRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CheckExpireChanceTest extends TestCase
{
    private ChanceRepository $chanceRepository;
    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $entityManager;
    private CheckExpireChance $command;

    protected function setUp(): void
    {
        $this->chanceRepository = $this->createMock(ChanceRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->command = new CheckExpireChance(
            $this->chanceRepository,
            $this->eventDispatcher,
            $this->entityManager
        );
    }

    public function test_constructor_createsInstance(): void
    {
        $this->assertInstanceOf(CheckExpireChance::class, $this->command);
        $this->assertInstanceOf(Command::class, $this->command);
    }

    public function test_execute_withNoExpiredChances_returnsSuccess(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $this->chanceRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('a.valid = true AND a.expireTime <= :now')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('now', $this->isType('string'))
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with(1000)
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([]);

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_withExpiredChances_processesThemCorrectly(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        
        $chance1 = $this->createMock(Chance::class);
        $chance2 = $this->createMock(Chance::class);
        $expiredChances = [$chance1, $chance2];
        
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $this->chanceRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->once())
            ->method('where')
            ->with('a.valid = true AND a.expireTime <= :now')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('now', $this->isType('string'))
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with(1000)
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn($expiredChances);

        // 验证每个过期机会的处理
        $chance1->expects($this->once())
            ->method('setValid')
            ->with(false);
        $chance1->expects($this->once())
            ->method('setStatus')
            ->with(ChanceStatusEnum::EXPIRED);
            
        $chance2->expects($this->once())
            ->method('setValid')
            ->with(false);
        $chance2->expects($this->once())
            ->method('setStatus')
            ->with(ChanceStatusEnum::EXPIRED);

        // 验证EntityManager的操作
        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->with($this->isInstanceOf(Chance::class));
        $this->entityManager->expects($this->exactly(2))
            ->method('flush');

        // 验证事件调度
        $this->eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof AfterChanceExpireEvent;
            }));

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_commandName_isCorrect(): void
    {
        $this->assertSame('lottery:check-expire-chance', CheckExpireChance::NAME);
        $this->assertSame(CheckExpireChance::NAME, $this->command->getName());
    }

    public function test_execute_setsCorrectParameterValue(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $this->chanceRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
            
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('now', $this->callback(function ($value) {
                // 验证时间参数格式正确
                return is_string($value) && 
                       preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value);
            }))
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->willReturnSelf();
            
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
            
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([]);

        $this->command->run($input, $output);
    }
} 