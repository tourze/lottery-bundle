<?php

namespace LotteryBundle\Tests\Exception;

use LotteryBundle\Exception\LotteryException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(LotteryException::class)]
final class LotteryExceptionTest extends AbstractExceptionTestCase
{
    public function testInstanceIsInstanceOfExpectedClasses(): void
    {
        $exception = new LotteryException('test message');

        $this->assertInstanceOf(LotteryException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testMessageIsSetCorrectly(): void
    {
        $message = 'Test exception message';
        $exception = new LotteryException($message);

        $this->assertEquals($message, $exception->getMessage());
    }
}
