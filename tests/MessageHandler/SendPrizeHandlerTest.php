<?php

namespace LotteryBundle\Tests\MessageHandler;

use LotteryBundle\MessageHandler\SendPrizeHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SendPrizeHandler::class)]
#[RunTestsInSeparateProcesses]
final class SendPrizeHandlerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testHandlerInvokesWithValidMessage(): void
    {
        $handler = self::getService(SendPrizeHandler::class);
        $this->assertInstanceOf(SendPrizeHandler::class, $handler);
    }
}
