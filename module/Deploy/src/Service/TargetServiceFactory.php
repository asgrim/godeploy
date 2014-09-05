<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TargetServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $targetMapper = $serviceLocator->get('Deploy\Mapper\Target');

        return new TargetService($targetMapper);
    }
}
