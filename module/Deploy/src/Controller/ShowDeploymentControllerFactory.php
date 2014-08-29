<?php

namespace Deploy\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ShowDeploymentControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = $serviceLocator->getServiceLocator();

        $projectService = $serviceManager->get('\Deploy\Service\ProjectService');
        $deploymentService = $serviceManager->get('\Deploy\Service\DeploymentService');

        return new ShowDeploymentController($projectService, $deploymentService);
    }
}
