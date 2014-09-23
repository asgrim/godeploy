<?php

namespace Deploy\Git;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GitRepositoryFactory implements FactoryInterface
{
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$sshOptions = $serviceLocator->get('Deploy\Options\SshOptions');
		$gitOptions = $serviceLocator->get('Deploy\Options\GitOptions');

		return new GitRepository($gitOptions, $sshOptions);
	}
}
