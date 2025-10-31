<?php

namespace LotteryBundle\Tests\Exception;

use LotteryBundle\Exception\PrizeServiceException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(PrizeServiceException::class)]
final class PrizeServiceExceptionTest extends AbstractExceptionTestCase
{
    public function testChancePoolCannotBeNull(): void
    {
        $exception = PrizeServiceException::chancePoolCannotBeNull();

        $this->assertInstanceOf(PrizeServiceException::class, $exception);
        $this->assertEquals('Chance pool cannot be null', $exception->getMessage());
    }

    public function testChanceUserCannotBeNull(): void
    {
        $exception = PrizeServiceException::chanceUserCannotBeNull();

        $this->assertInstanceOf(PrizeServiceException::class, $exception);
        $this->assertEquals('Chance user cannot be null', $exception->getMessage());
    }

    public function testPrizeTypeIdCannotBeNull(): void
    {
        $exception = PrizeServiceException::prizeTypeIdCannotBeNull();

        $this->assertInstanceOf(PrizeServiceException::class, $exception);
        $this->assertEquals('Prize typeId cannot be null', $exception->getMessage());
    }

    public function testChanceActivityCannotBeNull(): void
    {
        $exception = PrizeServiceException::chanceActivityCannotBeNull();

        $this->assertInstanceOf(PrizeServiceException::class, $exception);
        $this->assertEquals('Chance activity cannot be null', $exception->getMessage());
    }

    public function testExceptionExtendsException(): void
    {
        $exception = PrizeServiceException::chancePoolCannotBeNull();

        $this->assertInstanceOf(\Exception::class, $exception);
    }
}
