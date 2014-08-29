<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeployServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sshOptions = $serviceLocator->get('Deploy\Options\SshOptions');

        return new DeployService($sshOptions);
    }
}
