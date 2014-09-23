<?php

namespace Deploy\Git;

use Zend\ServiceManager\ServiceLocatorInterface;

class GitRepositoryFactory
{
	public static function createRepository(ServiceLocatorInterface $serviceLocator, $gitUrl)
	{
		$sshOptions = $serviceLocator->get('Deploy\Options\SshOptions');
		$gitOptions = $serviceLocator->get('Deploy\Options\GitOptions');

		$repo = new GitRepository($gitOptions, $sshOptions, $gitUrl);
		return $repo;
	}
}
