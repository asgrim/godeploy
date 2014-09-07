<?php

namespace Deploy\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdditionalFileServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $additionalFileMapper = $serviceLocator->get('Deploy\Mapper\AdditionalFile');

        return new AdditionalFileService($additionalFileMapper);
    }
}
