<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 the authors listed in AUTHORS file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author See AUTHORS file
 * @link http://www.godeploy.com/
 */
class DeployController extends Zend_Controller_Action
{
	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$this->view->headTitle('New Deployment');
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/js/pages/deploy_setup.js");
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/deploy.css");

		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		if(is_null($project))
		{
			throw new GD_Exception("Project '{$project_slug}' was not set up.");
		}

		$this->view->project = $project;

		$form = new GDApp_Form_DeploymentSetup($project->getId());
		$this->view->form = $form;

		$deployments = new GD_Model_DeploymentsMapper();

		if($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getParams()))
			{
				$user = GD_Auth_Database::GetLoggedInUser();

				$server_id = $this->_request->getParam('serverId', false);

				$last_deployment = $deployments->getLastSuccessfulDeployment($project->getId(), $server_id);
				if(!is_null($last_deployment))
				{
					$from_rev = $last_deployment->getToRevision();
				}
				else
				{
					$from_rev = "";
				}

				$git = GD_Git::FromProject($project);
				$git->gitPull();

				$input_to_rev = $this->_request->getParam('toRevision', false);
				$comment = $this->_request->getParam('comment', '');

				try
				{
					$to_rev = $git->getFullHash($input_to_rev);

					$deployment = new GD_Model_Deployment();
					$deployment->setUsersId($user->getId())
							->setProjectsId($project->getId())
							->setWhen(date("Y-m-d H:i:s"))
							->setServersId($server_id)
							->setFromRevision($from_rev)
							->setToRevision($to_rev)
							->setComment($comment)
							->setDeploymentStatusesId(1);

					$deployments->save($deployment);

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

					$deployment_files = new GD_Model_DeploymentFilesMapper();
					$deployment_file_statuses = new GD_Model_DeploymentFileStatusesMapper();
					$deployment_file_actions = new GD_Model_DeploymentFileActionsMapper();
					foreach($files_changed as $fc)
					{
						$deployment_file = new GD_Model_DeploymentFile();
						$deployment_file->setDeploymentsId($deployment->getId());
						$deployment_file->setDeploymentFileActionsId($deployment_file_actions->getDeploymentFileActionByGitStatus($fc['action'])->getId());
						$deployment_file->setDeploymentFileStatusesId($deployment_file_statuses->getDeploymentFileStatusByCode('NEW')->getId());
						$deployment_file->setDetails($fc['file']);

						$deployment_files->save($deployment_file);
					}

					// Forward to either run or preview page...
					if($this->_request->getParam('submitRun_x') > 0)
					{
						// go to run
						$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/run/" . $deployment->getId());
					}
					else if($this->_request->getParam('submitPreview_x') > 0)
					{
						// go to preview
						$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/preview/" . $deployment->getId());
					}
				}
				catch(GD_Exception $ex)
				{
					if($ex->getStringCode() === GD_Git::GIT_GENERAL_EMPTY_REF)
					{
						$form->toRevision->addError("Empty ref: " . $ex->getMessage());
					}
					else if($ex->getStringCode() === GD_Git::GIT_GENERAL_INVALID_REF)
					{
						$form->toRevision->addError("This revision could not be found in this project. Please check it.");
						$form->addError("error");
					}
					else
					{
						throw $ex;
					}
				}
			}
		}
		else
		{
			$git = GD_Git::FromProject($project);

			try
			{
				$git->checkValidRepository();
			}
			catch(GD_Exception $ex)
			{
				if($ex->getStringCode() == GD_Git::GIT_STATUS_ERROR_NOT_A_REPOSITORY
					|| $ex->getStringCode() == GD_Git::GIT_STATUS_ERROR_DIFFERENT_REPOSITORY)
				{
					$return_url = base64_encode($this->_request->getRequestUri());
					$this->_redirect($this->getFrontController()->getBaseUrl() . "/error/reclone?project=" . $this->_getParam("project") . "&return=" . $return_url);
				}
				else
				{
					throw $ex;
				}
			}

			$data = array();

			if(count($data) > 0)
			{
				$form->populate($data);
			}
		}
	}

	private function prepareStandardDeployInformation()
	{
		$this->_helper->viewRenderer('main');
		$this->view->headLink()->appendStylesheet("/css/template/table.css");
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/deploy.css");

		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		if(is_null($project))
		{
			throw new GD_Exception("Project '{$project_slug}' was not set up.");
		}
		$this->view->project = $project;

		$deployments = new GD_Model_DeploymentsMapper();
		$deployment = new GD_Model_Deployment();
		$deployments->find($this->_getParam('id'), $deployment);
		$this->view->deployment = $deployment;

		$deployment_files = new GD_Model_DeploymentFilesMapper();
		$file_list = $deployment_files->getDeploymentFilesByDeployment($deployment->getId());
		$this->view->file_list = $file_list;
	}

	public function previewAction()
	{
		if($this->getRequest()->isPost())
		{
			if($this->_getParam('btn_run_x') > 0)
			{
				$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/run/" . $this->_getParam("id"));
			}
			else if($this->_getParam('btn_back_x') > 0)
			{
				$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy");
			}
		}

		$this->prepareStandardDeployInformation();

		$status = $this->view->deployment->getDeploymentStatusesId();

		if($status == 2)
		{
			// Redirect to running page
			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/run/" . $this->_getParam("id"));
		}
		else if($status == 3 || $status == 4)
		{
			// Redirect to result page
			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/result/" . $this->_getParam("id"));
		}

		$this->view->mode = 'PREVIEW';

		$this->view->headTitle('Preview Deployment');
	}

	public function runAction()
	{
		$this->prepareStandardDeployInformation();

		$status = $this->view->deployment->getDeploymentStatusesId();

		if($status == 1)
		{
			// Status is previewing, commence deployment
			$this->view->headScript()->appendFile("/js/pages/deploy_run.js");
			$this->view->mode = 'RUN';
		}
		else if($status == 2)
		{
			// Status is running, don't start a new deployment just update
			$this->view->headScript()->appendFile("/js/pages/deploy_run.js");
			$this->view->mode = 'RUNNING';
		}
		else if($status == 3 || $status == 4)
		{
			// Redirect to result page
			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/result/" . $this->_getParam("id"));
		}
		$this->view->headTitle('Deploying...');
	}

	public function resultAction()
	{
		if($this->getRequest()->isPost())
		{
			if($this->_getParam('btn_history_x') > 0)
			{
				$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/history");
			}
		}

		$this->prepareStandardDeployInformation();

		$status = $this->view->deployment->getDeploymentStatusesId();

		if($status == 1)
		{
			// Status is previewing
			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/preview/" . $this->_getParam("id"));
		}
		else if($status == 2)
		{
			// Status is running, don't start a new deployment just update
			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/run/" . $this->_getParam("id"));
		}

		$this->view->mode = 'RESULT';
		$this->view->headTitle('Deployment Results');
	}

	public function executeDeploymentStatusAction()
	{
		// Project information
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		if(is_null($project))
		{
			throw new GD_Exception("Project '{$project_slug}' was not set up.");
		}

		$deployments = new GD_Model_DeploymentsMapper();
		$deployment = new GD_Model_Deployment();
		$deployments->find($this->_getParam('id'), $deployment);

		$deployment_files = new GD_Model_DeploymentFilesMapper();
		$file_list = $deployment_files->getDeploymentFilesByDeployment($deployment->getId());

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

		$deployment_status = $deployment->getDeploymentStatus()->getName();

		if(in_array($deployment->getDeploymentStatusesId(), array(3, 4)))
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
			"OVERALL_ICON" => $deployment->getDeploymentStatus()->getImageName(),
			"COMPLETE" => $complete,
		);

		// Output stuff
		$this->_response->setHeader('Content-type', 'text/plain');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$jsonData = Zend_Json::encode($data);
		$this->_response->appendBody($jsonData);
	}

	public function executeDeploymentStartAction()
	{
		// session_start blocks other requests, so close the session for the
		// status AJAX request to work
		Zend_Session::writeClose();

		ob_start();
		GD_Debug::StartDeploymentLog($this->_getParam("id"));

		GD_Debug::Log("Setting time limit... ", GD_Debug::DEBUG_BASIC, false);
		set_time_limit(0);
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		// Project information
		GD_Debug::Log("Loading project... ", GD_Debug::DEBUG_BASIC, false);
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		if(is_null($project))
		{
			throw new GD_Exception("Project '{$project_slug}' was not set up.");
		}
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		// Deployment information
		GD_Debug::Log("Loading deployment information... ", GD_Debug::DEBUG_BASIC, false);
		$deployments = new GD_Model_DeploymentsMapper();
		$deployment = new GD_Model_Deployment();
		$deployments->find($this->_getParam('id'), $deployment);
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		// Server information
		GD_Debug::Log("Loading server information... ", GD_Debug::DEBUG_BASIC, false);
		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();
		$servers->find($deployment->getServersId(), $server);
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		// Update the deployment status to show we're now running
		GD_Debug::Log("Updating deployment status to running... ", GD_Debug::DEBUG_BASIC, false);
		$deployment->setDeploymentStatusesId(2); // Running
		$deployments->save($deployment);
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		// Perform a git pull to check we're up to date
		$git = GD_Git::FromProject($project);
		$git->gitPull();

		// File list to action
		GD_Debug::Log("Getting file list... ", GD_Debug::DEBUG_BASIC, false);
		$deployment_files = new GD_Model_DeploymentFilesMapper();
		$deployment_files_statuses = new GD_Model_DeploymentFileStatusesMapper();
		$file_list = $deployment_files->getDeploymentFilesByDeployment($deployment->getId());
		GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		// Check out the revision we want to upload from
		GD_Debug::Log("Checking out revision {$deployment->getToRevision()}... ", GD_Debug::DEBUG_BASIC, false);
		$previous_ref = $git->getCurrentBranch(true);
		$res = $git->gitCheckout($deployment->getToRevision());
		if(!$res) GD_Debug::Log("FAILED.", GD_Debug::DEBUG_BASIC, true, false);
		else GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

		$errors = false;

		// Do the upload
		GD_Debug::Log("Actioning files now.", GD_Debug::DEBUG_BASIC);
		$ftp = new GD_Ftp($server);
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
			GD_Debug::Log("Actioning '{$file->getDetails()}'... ", GD_Debug::DEBUG_BASIC, false);
			$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('IN_PROGRESS')->getId());
			$deployment_files->save($file);

			try
			{
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
				$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('COMPLETE')->getId());
				GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);
			}
			catch(GD_Exception $ex)
			{
				$errors = true;
				$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('FAILED')->getId());
				GD_Debug::Log("FAILED [" . $ex->getMessage() . "].", GD_Debug::DEBUG_BASIC, true, false);
			}
			$deployment_files->save($file);
		}

		// Revert to previous revision
		GD_Debug::Log("Checking out revision {$previous_ref}... ", GD_Debug::DEBUG_BASIC, false);
		$res = $git->gitCheckout($previous_ref);
		if(!$res) GD_Debug::Log("FAILED.", GD_Debug::DEBUG_BASIC, true, false);
		else GD_Debug::Log("done.", GD_Debug::DEBUG_BASIC, true, false);

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
		flush();
		die();
	}

	public function getLastDeploymentRevisionAction()
	{
		// Get server ID from url
		$server_id = $this->_getParam("server_id");

		// Get project ID from url
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		$deployments = new GD_Model_DeploymentsMapper();
		$last_deployment = $deployments->getLastSuccessfulDeployment($project->getId(), $server_id);
		if(!is_null($last_deployment))
		{
			$from_rev = $last_deployment->getToRevision();
		}
		else
		{
			$from_rev = "";
		}

		$this->_response->setHeader('Content-type', 'text/plain');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$data = array(
			'fromRevision' => $from_rev,
		);

		$jsonData = Zend_Json::encode($data);
		$this->_response->appendBody($jsonData);
	}

	public function getLatestRevisionAction()
	{
		// Get project ID from url
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		// Git pull before anything
		$git = GD_Git::FromProject($project);
		$git->gitPull();

		$data = array();

		$last_commit = $git->getLastCommit();
		if(is_array($last_commit))
		{
			$to_revision = $last_commit['HASH'];
			$data['toRevision'] = $to_revision;
		}

		$this->_response->setHeader('Content-type', 'text/plain');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$jsonData = Zend_Json::encode($data);
		$this->_response->appendBody($jsonData);
	}
}

