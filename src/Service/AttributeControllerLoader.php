<?php

namespace LotteryBundle\Service;

use LotteryBundle\Controller\H5\LotteryAddressController;
use LotteryBundle\Controller\H5\LotteryIndexController;
use LotteryBundle\Controller\H5\LotteryRecordsController;
use LotteryBundle\Controller\H5\LotteryRulesController;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

#[AutoconfigureTag('routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    private AttributeRouteControllerLoader $controllerLoader;

    public function __construct()
    {
        parent::__construct();
        $this->controllerLoader = new AttributeRouteControllerLoader();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(LotteryIndexController::class));
        $collection->addCollection($this->controllerLoader->load(LotteryAddressController::class));
        $collection->addCollection($this->controllerLoader->load(LotteryRecordsController::class));
        $collection->addCollection($this->controllerLoader->load(LotteryRulesController::class));
        return $collection;
    }
}
