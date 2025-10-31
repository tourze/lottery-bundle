<?php

declare(strict_types=1);

namespace LotteryBundle\Tests;

use LotteryBundle\LotteryBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(LotteryBundle::class)]
#[RunTestsInSeparateProcesses]
final class LotteryBundleTest extends AbstractBundleTestCase
{
}
