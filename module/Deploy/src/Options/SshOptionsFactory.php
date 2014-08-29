<?php

namespace Deploy\Options;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SshOptionsFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['deploy']) || !isset($config['deploy']['ssh-options']))
        {
            throw new \Exception("Unable to find ssh options in configuration");
        }

        return new SshOptions($config['deploy']['ssh-options']);
    }
}
