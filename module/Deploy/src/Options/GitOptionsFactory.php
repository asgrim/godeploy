<?php

namespace Deploy\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GitOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['deploy']) || !isset($config['deploy']['git-options'])) {
            throw new \Exception("Unable to find git options in configuration");
        }

        return new GitOptions($config['deploy']['git-options']);
    }
}
