<?php

namespace LotteryBundle\Tests\Integration\Service;

use LotteryBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\TestCase;

class AttributeControllerLoaderTest extends TestCase
{
    public function test_instance_isInstanceOfExpectedClass(): void
    {
        $service = new AttributeControllerLoader();
        
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }
}