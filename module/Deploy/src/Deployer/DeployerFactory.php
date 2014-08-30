<?php

namespace Deploy\Deployer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeployerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sshOptions = $serviceLocator->get('Deploy\Options\SshOptions');
        $projectService = $serviceLocator->get('\Deploy\Service\ProjectService');
        $targetService = $serviceLocator->get('\Deploy\Service\TargetService');

        return new Deployer($sshOptions, $projectService, $targetService);
    }
}
