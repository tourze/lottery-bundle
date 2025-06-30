<?php

namespace LotteryBundle\Tests\Command;

use LotteryBundle\Command\CheckReviewChanceToSendPrize;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Service\PrizeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckReviewChanceToSendPrizeTest extends TestCase
{
    public function test_constructor_createsInstance(): void
    {
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $prizeService = $this->createMock(PrizeService::class);
        $command = new CheckReviewChanceToSendPrize($chanceRepository, $prizeService);

        $this->assertInstanceOf(CheckReviewChanceToSendPrize::class, $command);
        $this->assertInstanceOf(Command::class, $command);
    }

    public function test_getName_returnsCorrectName(): void
    {
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $prizeService = $this->createMock(PrizeService::class);
        $command = new CheckReviewChanceToSendPrize($chanceRepository, $prizeService);

        $this->assertSame('lottery:check-review-chance-send-prize', $command->getName());
    }

    public function test_execute_withValidInput_returnsSuccess(): void
    {
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $prizeService = $this->createMock(PrizeService::class);
        $command = new CheckReviewChanceToSendPrize($chanceRepository, $prizeService);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $result = $command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_getDescription_returnsCorrectDescription(): void
    {
        $chanceRepository = $this->createMock(ChanceRepository::class);
        $prizeService = $this->createMock(PrizeService::class);
        $command = new CheckReviewChanceToSendPrize($chanceRepository, $prizeService);

        $this->assertStringContainsString('已审核的机会需要发奖', $command->getDescription());
    }
} 