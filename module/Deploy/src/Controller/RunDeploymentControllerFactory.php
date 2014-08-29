<?php

namespace Deploy\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RunDeploymentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = $serviceLocator->getServiceLocator();

        $deployer = $serviceManager->get('\Deploy\Deployer\Deployer');
        $deploymentService = $serviceManager->get('\Deploy\Service\DeploymentService');

        return new RunDeploymentController($deployer, $deploymentService);
    }
}
