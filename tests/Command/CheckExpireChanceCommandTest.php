<?php

namespace LotteryBundle\Tests\Command;

use LotteryBundle\Command\CheckExpireChanceCommand;
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
#[CoversClass(CheckExpireChanceCommand::class)]
#[RunTestsInSeparateProcesses]
final class CheckExpireChanceCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 这个方法必须实现，但可以为空
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(CheckExpireChanceCommand::class);

        return new CommandTester($command);
    }

    public function testConstructorCreatesInstance(): void
    {
        $command = self::getService(CheckExpireChanceCommand::class);
        $this->assertInstanceOf(CheckExpireChanceCommand::class, $command);
        $this->assertInstanceOf(Command::class, $command);
    }

    public function testExecuteReturnsSuccess(): void
    {
        $command = self::getService(CheckExpireChanceCommand::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $result = $command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function testCommandNameIsCorrect(): void
    {
        $command = self::getService(CheckExpireChanceCommand::class);
        $this->assertSame('lottery:check-expire-chance', CheckExpireChanceCommand::NAME);
        $this->assertSame(CheckExpireChanceCommand::NAME, $command->getName());
    }

    public function testExecuteWithCommandTester(): void
    {
        $command = self::getService(CheckExpireChanceCommand::class);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
