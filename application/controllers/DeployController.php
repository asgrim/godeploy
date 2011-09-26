<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 James Titcumb, Simon Wade
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
 * @author James Titcumb, Simon Wade
 * @link http://www.godeploy.com/
 */
class DeployController extends Zend_Controller_Action
{
	private $_enable_debug = false;
	private $_debug_fh;

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
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

				$git = new GD_Git($project);
				$git->gitPull();

				$input_to_rev = $this->_request->getParam('toRevision', false);

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
							->setDeploymentStatusesId(1);

					$deployments->save($deployment);

					// Generate the list of files to deploy and save in deployment_files table
					$files_changed = $git->getFilesChangedList($deployment->getFromRevision(), $deployment->getToRevision());

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
			$git = new GD_Git($project);

			try
			{
				$git->checkValidRepository();
			}
			catch(GD_Exception $ex)
			{
				if($ex->getStringCode() == GD_Git::GIT_STATUS_ERROR_NOT_A_REPOSITORY
					|| $ex->getStringCode() == GD_Git::GIT_STATUS_ERROR_DIFFERENT_REPOSITORY)
				{
					$return_url = base64_encode($_SERVER['SCRIPT_URI']);
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

	private function writeDebug($debug)
	{
		if(!$this->_enable_debug) return;

		if(!$this->_debug_fh)
		{
			$logfile = sys_get_temp_dir() . "/gd_deploy_log";
			$this->_debug_fh = fopen($logfile, "a");
			chmod($logfile, 0755);
			fwrite($this->_debug_fh, "===============================================================================\n");
			fwrite($this->_debug_fh, "Deployment ID " . $this->_getParam("id") . " started " . date("Y-m-d H:i:s") . "\n");
			fwrite($this->_debug_fh, "===============================================================================\n");
		}
		fwrite($this->_debug_fh, $debug);
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

		$this->view->mode = 'PREVIEW';
	}

	public function runAction()
	{
		$this->prepareStandardDeployInformation();

		if($this->view->deployment->getDeploymentStatusesId() == 1)
		{
			$this->view->headScript()->appendFile("/js/pages/deploy_run.js");
			$this->view->mode = 'RUN';
		}
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

		$this->view->mode = 'RESULT';
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

		$data = array(
			"FILES" => $file_statuses,
			"OVERALL" => $deployment_status,
			"COMPLETE" => $complete,
		);

		// Output stuff
		$this->_response->setHeader('Content-type','text/plain');
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
		$this->writeDebug("Setting time limit... ");
		set_time_limit(0);
		$this->writeDebug("done.\n");

		// Project information
		$this->writeDebug("Loading project... ");
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
		$this->writeDebug("done.\n");

		// Deployment information
		$this->writeDebug("Loading deployment information... ");
		$deployments = new GD_Model_DeploymentsMapper();
		$deployment = new GD_Model_Deployment();
		$deployments->find($this->_getParam('id'), $deployment);
		$this->writeDebug("done.\n");

		// Server information
		$this->writeDebug("Loading server information... ");
		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();
		$servers->find($deployment->getServersId(), $server);
		$this->writeDebug("done.\n");

		// Update the deployment status to show we're now running
		$this->writeDebug("Updating deployment status to running... ");
		$deployment->setDeploymentStatusesId(2); // Running
		$deployments->save($deployment);
		$this->writeDebug("done.\n");

		// Perform a git pull to check we're up to date
		$git = new GD_Git($project);
		$git->gitPull();

		// File list to action
		$this->writeDebug("Getting file list... ");
		$deployment_files = new GD_Model_DeploymentFilesMapper();
		$deployment_files_statuses = new GD_Model_DeploymentFileStatusesMapper();
		$file_list = $deployment_files->getDeploymentFilesByDeployment($deployment->getId());
		$this->writeDebug("done.\n");

		// Check out the revision we want to upload from
		$this->writeDebug("Checking out revision {$deployment->getToRevision()}... ");
		$previous_ref = $git->getCurrentBranch(true);
		$res = $git->gitCheckout($deployment->getToRevision());
		if(!$res) $this->writeDebug("FAILED.\n");
		else $this->writeDebug("done.\n");

		$errors = false;

		// Do the upload
		$this->writeDebug("Actioning files now.\n");
		$ftp = new GD_Ftp($server);
		try
		{
			$ftp->connect();
		}
		catch(GD_Exception $ex)
		{
			$this->writeDebug("FTP Connect failed: {$ex->getMessage()}\n");
		}
		foreach($file_list as $file)
		{
			$this->writeDebug("Actioning '{$file->getDetails()}'... ");
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
				$this->writeDebug("done.\n");
			}
			catch(GD_Exception $ex)
			{
				$errors = true;
				$file->setDeploymentFileStatusesId($deployment_files_statuses->getDeploymentFileStatusByCode('FAILED')->getId());
				$this->writeDebug("FAILED [" . $ex->getMessage() . "].\n");
			}
			$deployment_files->save($file);
		}

		// Revert to previous revision
		$this->writeDebug("Checking out revision {$previous_ref}... ");
		$res = $git->gitCheckout($previous_ref);
		if(!$res) $this->writeDebug("FAILED.\n");
		else $this->writeDebug("done.\n");

		$this->writeDebug("Setting deployment status " . ($errors ? "[errors]" : "[success]") . "... ");
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
		$this->writeDebug("done.\n");

		$this->writeDebug("All finished.\n");

		$buf = ob_get_contents();
		if($buf)
		{
			$this->writeDebug("Extra content:\n\n{$buf}");
		}
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

		$this->_response->setHeader('Content-type','text/plain');
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
		$git = new GD_Git($project);
		$git->gitPull();

		$data = array();

		$last_commit = $git->getLastCommit();
		if(is_array($last_commit))
		{
			$to_revision = $last_commit['HASH'];
			$data['toRevision'] = $to_revision;
		}

		$this->_response->setHeader('Content-type','text/plain');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$jsonData = Zend_Json::encode($data);
		$this->_response->appendBody($jsonData);
	}
}

