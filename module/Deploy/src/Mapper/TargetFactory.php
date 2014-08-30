<?php

namespace Deploy\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TargetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $targetMapper = new Target();

        $targetMapper
            ->setDbAdapter($dbAdapter)
            ->setHydrator(new TargetHydrator())
            ->setEntityPrototype(new \Deploy\Entity\Target())
        ;

        return $targetMapper;
    }
}
