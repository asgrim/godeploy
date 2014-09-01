<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeploymentLogServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $deploymentLogMapper = $serviceLocator->get('Deploy\Mapper\DeploymentLog');
        return new DeploymentLogService($deploymentLogMapper);
    }
}
