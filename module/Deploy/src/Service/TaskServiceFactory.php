<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TaskServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $taskMapper = $serviceLocator->get('Deploy\Mapper\Task');

        return new TaskService($taskMapper);
    }
}
