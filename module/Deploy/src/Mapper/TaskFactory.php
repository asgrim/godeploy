<?php

namespace Deploy\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TaskFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $taskMapper = new Task();

        $taskMapper
            ->setDbAdapter($dbAdapter)
            ->setHydrator(new TaskHydrator())
            ->setEntityPrototype(new \Deploy\Entity\Task())
        ;

        return $taskMapper;
    }
}
