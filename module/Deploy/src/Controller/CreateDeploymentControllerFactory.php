<?php

namespace Deploy\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CreateDeploymentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = $serviceLocator->getServiceLocator();

        $projectService = $serviceManager->get('\Deploy\Service\ProjectService');
        $deployService = $serviceManager->get('\Deploy\Service\DeployService');

        return new CreateDeploymentController($projectService, $deployService);
    }
}
