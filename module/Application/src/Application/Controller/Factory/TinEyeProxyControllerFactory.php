<?php

namespace Application\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\TinEyeProxyController;


class TinEyeProxyControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
 
        $controller = new TinEyeProxyController($sl->get('config')['tineye']);
        return $controller;
    }
}