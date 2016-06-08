<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\SearchController;
use Application\Service\UtilitiesService;
use Application\Service\TinEyeService;

class SearchControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        $controller = new SearchController(new UtilitiesService(), new TinEyeService(), $sl->get('config')['tinEye']);
        return $controller;
    }
}