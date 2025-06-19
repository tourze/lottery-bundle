<?php

namespace LotteryBundle\Command;

use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Service\PrizeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('43 * * * *')]
#[AsCommand(name: self::NAME, description: '已审核的机会需要发奖')]
class CheckReviewChanceToSendPrize extends Command
{
    public const NAME = 'lottery:check-review-chance-send-prize';
    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly PrizeService $prizeService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $chances = $this->chanceRepository->createQueryBuilder('a')
            ->where('a.prize is not null AND a.user is not null AND a.status = :status AND a.sendTime is null')
            ->setParameter('status', ChanceStatusEnum::REVIEWED)
            ->setMaxResults(1000)
            ->getQuery()
            ->toIterable();
        foreach ($chances as $chance) {
            $output->writeln("{$chance->getId()},奖品：{$chance->getPrize()->getName()}");
            $this->prizeService->sendPrize($chance);
        }

        return Command::SUCCESS;
    }
}
