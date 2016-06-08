<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\SearchController;
use Application\Service\ImageService;
use Application\Service\TinEyeService;

class SearchControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $controller = new SearchController(new ImageService());
        return $controller;
    }
}