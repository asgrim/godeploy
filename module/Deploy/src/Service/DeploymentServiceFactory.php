<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeploymentServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $deploymentMapper = $serviceLocator->get('Deploy\Mapper\Deployment');

        return new DeploymentService($deploymentMapper);
    }
}
