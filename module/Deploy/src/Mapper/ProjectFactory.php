<?php

namespace Deploy\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProjectFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $projectMapper = new Project();

        $projectMapper
            ->setDbAdapter($dbAdapter)
            ->setHydrator(new ProjectHydrator())
            ->setEntityPrototype(new \Deploy\Entity\Project())
        ;

        return $projectMapper;
    }
}
