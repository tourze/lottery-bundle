<?php

namespace LotteryBundle\Tests\Unit\Exception;

use LotteryBundle\Exception\LotteryException;
use PHPUnit\Framework\TestCase;

class LotteryExceptionTest extends TestCase
{
    public function test_instance_isInstanceOfExpectedClasses(): void
    {
        $exception = new LotteryException('test message');
        
        $this->assertInstanceOf(LotteryException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function test_message_isSetCorrectly(): void
    {
        $message = 'Test exception message';
        $exception = new LotteryException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }
}