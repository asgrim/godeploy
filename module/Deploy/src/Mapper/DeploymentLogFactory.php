<?php

namespace Deploy\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DeploymentLogFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $deploymentLogMapper = new DeploymentLog();

        $deploymentLogMapper
            ->setDbAdapter($dbAdapter)
            ->setHydrator(new DeploymentLogHydrator())
            ->setEntityPrototype(new \Deploy\Entity\DeploymentLog())
        ;

        return $deploymentLogMapper;
    }
}
