<?php

namespace LotteryBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Event\AfterChanceExpireEvent;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
#[AsCommand(name: self::NAME, description: '检查过期的机会数据')]
class CheckExpireChance extends Command
{
    public const NAME = 'lottery:check-expire-chance';
    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 如果不是几千上万条的话，我们就不做条数限制了
        $chances = $this->chanceRepository->createQueryBuilder('a')
            ->where('a.valid = true AND a.expireTime <= :now')
            ->setParameter('now', CarbonImmutable::now()->format('Y-m-d H:i:s'))
            ->setMaxResults(1000) // 1000条够多了吧
            ->getQuery()
            ->toIterable();
        foreach ($chances as $chance) {
            /* @var Chance $chance */
            $chance->setValid(false);
            $chance->setStatus(ChanceStatusEnum::EXPIRED);
            $this->entityManager->persist($chance);
            $this->entityManager->flush();

            $event = new AfterChanceExpireEvent();
            $event->setChance($chance);
            $this->eventDispatcher->dispatch($event);
        }

        return Command::SUCCESS;
    }
}
