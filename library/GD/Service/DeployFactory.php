<?php

class GD_Service_DeployFactory
{
	/**
	 * Used when no deployment already exists
	 *
	 * @param GD_Model_User $user
	 * @param GD_Model_Server $server
	 * @param GD_Model_Project $project
	 *
	 * @return GD_Service_Deploy
	 */
	public static function factoryFromModels(GD_Model_User $user, GD_Model_Server $server, GD_Model_Project $project)
	{
		$deploy_service = new GD_Service_Deploy();

		$deploy_service
			->setUser($user)
			->setServer($server)
			->setProject($project);

		return $deploy_service;
	}

	/**
	 * Used when a deployment already exists
	 *
	 */
	public static function factoryFromDeployment(GD_Model_Deployment $deployment)
	{
		$deploy_service = new GD_Service_Deploy();

		$deploy_service
			->setUser($deployment->getUser())
			->setProject($deployment->getProject())
			->setServer($deployment->getServer())
			->setDeployment($deployment);

		return $deploy_service;
	}
}