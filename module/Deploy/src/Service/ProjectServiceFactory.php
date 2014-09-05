<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProjectServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $projectMapper = $serviceLocator->get('Deploy\Mapper\Project');

        return new ProjectService($projectMapper);
    }
}
