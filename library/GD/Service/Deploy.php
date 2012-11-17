<?php

class GD_Service_Deploy
{
	protected $User;
	protected $Server;
	protected $Project;
	protected $Deployment;

	public function setUser(GD_Model_User $user)
	{
		$this->User = $user;
		return $this;
	}

	public function getUser()
	{
		return $this->User;
	}

	public function setServer(GD_Model_Server $server)
	{
		$this->Server = $server;
		return $this;
	}

	public function getServer()
	{
		return $this->Server;
	}

	public function setProject(GD_Model_Project $project)
	{
		$this->Project = $project;
		return $this;
	}

	public function getProject()
	{
		return $this->Project;
	}

	public function setDeployment(GD_Model_Deployment $deployment)
	{
		$this->Deployment = $deployment;
		return $this;
	}

	public function getDeployment()
	{
		return $this->Deployment;
	}

	/**
	 * Get the last deployed revision for a deployment
	 *
	 * @return string
	 */
	public function getLastDeployedRevision()
	{
		$deployments = new GD_Model_DeploymentsMapper();

		$last_deployment = $deployments->getLastSuccessfulDeployment($this->getProject()->getId(), $this->getServer()->getId());

		if(!is_null($last_deployment))
		{
			$from_rev = $last_deployment->getToRevision();
		}
		else
		{
			$from_rev = "";
		}

		return $from_rev;
	}

	/**
	 * Create a deployment for this service (and save to the DB)
	 *
	 * @param string $to_revision A valid git revision
	 * @param string $comment Optional deployment comment
	 * @return GD_Model_Deployment
	 */
	public function createDeployment($to_revision, $comment = '')
	{

		$from_rev = $this->getLastDeployedRevision();

		$git = GD_Git::FromProject($this->getProject());
		$git->gitPull();

		if ($to_revision == "latest")
		{
			$last_commit = $git->getLastCommit();
			$to_revision = $last_commit['HASH'];
		}

		$to_rev = $git->getFullHash($to_revision);

		$deployments = new GD_Model_DeploymentsMapper();

		$deployment = new GD_Model_Deployment();
		$deployment
			->setUser($this->getUser())
			->setUsersId($this->getUser()->getId())
			->setProject($this->getProject())
			->setProjectsId($this->getProject()->getId())
			->setWhen(date("Y-m-d H:i:s"))
			->setServer($this->getServer())
			->setServersId($this->getServer()->getId())
			->setFromRevision($from_rev)
			->setToRevision($to_rev)
			->setComment($comment)
			->setDeploymentStatusesId(1);

		$deployments->save($deployment);

		$this->createDeploymentFiles($git, $deployment);

		$this->setDeployment($deployment);

		return $deployment;
	}

	protected function createDeploymentFiles(GD_Git $git, GD_Model_Deployment $deployment)
	{
		$deployment_files = new GD_Model_DeploymentFilesMapper();
		$deployment_file_statuses = new GD_Model_DeploymentFileStatusesMapper();
		$deployment_file_actions = new GD_Model_DeploymentFileActionsMapper();
		$config_servers_map = new GD_Model_ConfigsServersMapper();

		// Generate the list of files to deploy and save in deployment_files table
		try
		{
			$files_changed = $git->getFilesChangedList($deployment->getFromRevision(), $deployment->getToRevision());
		}
		catch(GD_Exception $ex)
		{
			if($ex->getStringCode() == GD_Git::GIT_GENERAL_NO_FILES_CHANGED)
			{
				$files_changed = array();
			}
			else throw $ex;
		}

		foreach($files_changed as $fc)
		{
			$deployment_file = new GD_Model_DeploymentFile();
			$deployment_file->setDeploymentsId($deployment->getId());
			$deployment_file->setDeploymentFileActionsId($deployment_file_actions->getDeploymentFileActionByGitStatus($fc['action'])->getId());
			$deployment_file->setDeploymentFileStatusesId($deployment_file_statuses->getDeploymentFileStatusByCode('NEW')->getId());
			$deployment_file->setDetails($fc['file']);

			$deployment_files->save($deployment_file);
		}

		// Add any additional configuration files
		$configs = $config_servers_map->getAllConfigsForServer($this->getServer()->getId());

		foreach($configs as $config)
		{
			$c = $config->getConfig();
			$cfile = "!CFG!{$c->getId()}!{$c->getFilename()}";

			$deployment_file = new GD_Model_DeploymentFile();
			$deployment_file->setDeploymentsId($deployment->getId());
			$deployment_file->setDeploymentFileActionsId($deployment_file_actions->getDeploymentFileActionByGitStatus('M')->getId());
			$deployment_file->setDeploymentFileStatusesId($deployment_file_statuses->getDeploymentFileStatusByCode('NEW')->getId());
			$deployment_file->setDetails($cfile);

			$deployment_files->save($deployment_file);
		}
	}

	public function getDeploymentStatus()
	{
		$file_list = $this->getDeployment()->getDeploymentFiles();

		$file_statuses = array();
		$file_icons = array();

		$completed_count = 0;
		foreach($file_list as $file)
		{
			if($file->getDeploymentFileStatus()->getCode() == "IN_PROGRESS")
			{
				$file_statuses[$file->getId()] = $file->getDeploymentFileAction()->getVerb();
			}
			else
			{
				$file_statuses[$file->getId()] = $file->getDeploymentFileStatus()->getName();
			}

			$file_icons[$file->getId()] = $file->getDeploymentFileStatus()->getImageName();

			if($file->getDeploymentFileStatus()->getCode() != "NEW"
					&& $file->getDeploymentFileStatus()->getCode() != "IN_PROGRESS")
			{
				$completed_count++;
			}
		}

		$deployment_status = $this->getDeployment()->getDeploymentStatus()->getName();

		if(in_array($this->getDeployment()->getDeploymentStatusesId(), array(3, 4)))
		{
			$complete = true;
		}
		else
		{
			$complete = false;
		}

		$num_files = count($file_statuses);
		if($num_files > 0)
		{
			$cmp_text = " (" . ceil(($completed_count / $num_files) * 100) . "%)";
		}

		$data = array(
				"FILES" => $file_statuses,
				"FILE_ICONS" => $file_icons,
				"NUM_FILES" => $num_files,
				"OVERALL" => $deployment_status . $cmp_text,
				"OVERALL_ICON" => $this->getDeployment()->getDeploymentStatus()->getImageName(),
				"COMPLETE" => $complete,
		);

		return $data;
	}

	public function runDeployment($should_block = true)
	{
		if ($should_block)
		{
			return $this->runBlockingDeployment();
		}
		else
		{
			return $this->runNonBlockingDeployment();
		}
	}

	private function runNonBlockingDeployment()
	{
		throw new Exception("Not implemented");
	}

	/**
	 * This is a blocking action
	 *
	 */
	private function runBlockingDeployment()
	{
		ob_start();
		GD_Debug::StartDeploymentLog($this->getDeployment()->getId());

		set_time_limit(0);

		$deployments = new GD_Model_DeploymentsMapper();

		GD_Debug::Log("Loading deployment artifacts... ", GD_Debug::DEBUG_FULL);
		$deployment = $this->getDeployment();
		$server = $this->getServer();
		$project = $this->getProject();

		// Update the deployment status to show we're now running
		$deployment->setDeploymentStatusesId(2); // Running
		$deployments->save($deployment);

		// Perform a git pull to make sure we're up to date
		$git = GD_Git::FromProject($project);
		$git->gitPull();

		// File list to action
		$file_list = $deployment->getDeploymentFiles();
		GD_Debug::Log("Artifacts all loaded.", GD_Debug::DEBUG_FULL);

		// Check out the revision we want to upload from
		GD_Debug::Log("Checking out deployment revision {$deployment->getToRevision()}", GD_Debug::DEBUG_FULL);
		$previous_ref = $git->getCurrentBranch(true);
		$git->gitCheckout($deployment->getToRevision());

		$errors = false;

		// Do the upload
		GD_Debug::Log("Starting upload. ", GD_Debug::DEBUG_BASIC);
		$ftp = GD_Ftp::FromServer($this->getServer());
		try
		{
			$ftp->connect();
		}
		catch(GD_Exception $ex)
		{
			GD_Debug::Log("FTP Connect failed: {$ex->getMessage()}", GD_Debug::DEBUG_BASIC);
		}

		foreach($file_list as $file)
		{
			$this->deployFile($ftp, $git, $file);
		}
		GD_Debug::Log("Finished upload. ", GD_Debug::DEBUG_BASIC);

		// Revert to previous revision
		GD_Debug::Log("Checking out previous revision {$previous_ref}", GD_Debug::DEBUG_FULL);
		$git->gitCheckout($previous_ref);

		GD_Debug::Log("Setting deployment status " . ($errors ? "[errors]" : "[success]") . "... ", GD_Debug::DEBUG_BASIC, false);
		if($errors)
		{
			$deployment->setDeploymentStatusesId(4); // Failed
			$deployments->save($deployment);
		}
		else
		{
			$deployment->setDeploymentStatusesId(3); // Complete
			$deployments->save($deployment);
		}
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		GD_Debug::Log("All finished.", GD_Debug::DEBUG_BASIC);

		$buf = ob_get_contents();
		if($buf)
		{
			GD_Debug::Log("Extra content:\n\n{$buf}", GD_Debug::DEBUG_BASIC);
		}

		GD_Debug::EndDeploymentLog();
		ob_end_clean();

		if ($errors)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	private function deployConfigFile(GD_Ftp $ftp, GD_Model_Config $config, $remote_path)
	{
		// Configuration file - store in temp dir from DB then upload
		$tmpfile = tempnam(sys_get_temp_dir(), 'gdcfg');

		$config_content = $config->getProcessedConfig($this);

		file_put_contents($tmpfile, $config_content);

		$ftp->upload($tmpfile, $remote_path);

		unlink($tmpfile);
	}

	private function deployRegularFile(GD_Ftp $ftp, GD_Git $git, GD_Model_DeploymentFile $file)
	{
		// Regular file - upload as normal
		switch($file->getDeploymentFileAction()->getGitStatus())
		{
			case 'A':
			case 'M':
				$ftp->upload($git->getGitDir() . $file->getDetails(), $file->getDetails());
				break;
			case 'D':
				$ftp->delete($file->getDetails());
				break;
			default:
				throw GD_Exception("Warning, unhandled action: '" . $file->getDeploymentFileAction()->getGitStatus() . "' ({$file->getDetails()}");
				break;
		}
	}

	private function deployFile(GD_Ftp $ftp, GD_Git $git, GD_Model_DeploymentFile $file)
	{
		GD_Debug::Log("Uploading - " . $file->getDetails(), GD_Debug::DEBUG_BASIC);

		$deployment_files_statuses = new GD_Model_DeploymentFileStatusesMapper();
		$deployment_files = new GD_Model_DeploymentFilesMapper();
		$config_map = new GD_Model_ConfigsMapper();

		$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('IN_PROGRESS')->getId());
		$deployment_files->save($file);

		sleep(5);

		$matches = array();
		$is_config_file = preg_match('/^!CFG!(\d+)!(.*)$/', $file->getDetails(), $matches);

		try
		{
			if($is_config_file == 1)
			{
				$config = new GD_Model_Config();
				$config_map->find($matches[1], $config);

				$this->deployConfigFile($ftp, $config, $matches[2]);
			}
			else
			{
				$this->deployRegularFile($ftp, $git, $file);
			}
			$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('COMPLETE')->getId());
		}
		catch(GD_Exception $ex)
		{
			// Only fail the whole deployment if we're not a delete action
			if($file->getDeploymentFileAction()->getGitStatus() != 'D')
			{
				$errors = true;
			}

			$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('FAILED')->getId());
			GD_Debug::Log("FAILED [" . $ex->getMessage() . "].", GD_Debug::DEBUG_BASIC, true, false);
		}

		$deployment_files->save($file);
	}
}