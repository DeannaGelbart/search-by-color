<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\ConsoleController;
use Application\Service\ImageService;
use Application\Service\TinEyeService;

class ConsoleControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        $controller = new ConsoleController(new ImageService(), new TinEyeService(), $sl->get('config')['tinEye']);
        return $controller;
    }
}