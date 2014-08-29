<?php

namespace Deploy\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeploymentFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $deploymentMapper = new Deployment();

        $deploymentMapper
            ->setDbAdapter($dbAdapter)
            ->setHydrator(new DeploymentHydrator())
            ->setEntityPrototype(new \Deploy\Entity\Deployment())
        ;

        return $deploymentMapper;
    }
}
