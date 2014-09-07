<?php

namespace Deploy\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdditionalFileFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');

        $additionalFileMapper = new AdditionalFile();

        $additionalFileMapper
            ->setDbAdapter($dbAdapter)
            ->setHydrator(new AdditionalFileHydrator())
            ->setEntityPrototype(new \Deploy\Entity\AdditionalFile())
        ;

        return $additionalFileMapper;
    }
}
