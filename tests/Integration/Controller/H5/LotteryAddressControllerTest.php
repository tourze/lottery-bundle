<?php

namespace LotteryBundle\Tests\Integration\Controller\H5;

use LotteryBundle\Controller\H5\LotteryAddressController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LotteryAddressControllerTest extends TestCase
{
    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $controller = new LotteryAddressController();
        
        $this->assertInstanceOf(LotteryAddressController::class, $controller);
    }

    public function test_invoke_withoutChanceId_throwsNotFound(): void
    {
        $controller = new LotteryAddressController();
        $request = new Request();

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('抽奖记录ID不能为空');
        
        $controller->__invoke($request);
    }
}