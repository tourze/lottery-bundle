<?php

namespace LotteryBundle\Tests\Command;

use LotteryBundle\Command\CheckReviewChanceToSendPrizeCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(CheckReviewChanceToSendPrizeCommand::class)]
#[RunTestsInSeparateProcesses]
final class CheckReviewChanceToSendPrizeCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 这个方法必须实现，但可以为空
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(CheckReviewChanceToSendPrizeCommand::class);

        return new CommandTester($command);
    }

    public function testConstructorCreatesInstance(): void
    {
        $command = self::getService(CheckReviewChanceToSendPrizeCommand::class);

        $this->assertInstanceOf(CheckReviewChanceToSendPrizeCommand::class, $command);
        $this->assertInstanceOf(Command::class, $command);
    }

    public function testGetNameReturnsCorrectName(): void
    {
        $command = self::getService(CheckReviewChanceToSendPrizeCommand::class);

        $this->assertSame('lottery:check-review-chance-send-prize', $command->getName());
    }

    public function testExecuteWithValidInputReturnsSuccess(): void
    {
        $command = self::getService(CheckReviewChanceToSendPrizeCommand::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $result = $command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function testGetDescriptionReturnsCorrectDescription(): void
    {
        $command = self::getService(CheckReviewChanceToSendPrizeCommand::class);

        $this->assertStringContainsString('已审核的机会需要发奖', $command->getDescription());
    }

    public function testExecuteWithCommandTester(): void
    {
        $command = self::getService(CheckReviewChanceToSendPrizeCommand::class);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
