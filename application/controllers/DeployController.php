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

		$project = $this->_helper->getProjectFromUrl();

		$this->view->project = $project;

		$form = new GDApp_Form_DeploymentSetup($project->getId());

		if (GD_Config::get('force_preview') == '1')
		{
			$form->removeElement('submitRun');
			$previewElement = $form->getElement('submitPreview');
			$previewElement->class .= ' preview_only';
		}

		$this->view->form = $form;

		$deployments = new GD_Model_DeploymentsMapper();

		if($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getParams()))
			{
				$user = GD_Auth_Database::GetLoggedInUser();

				$server_id = $this->_request->getParam('serverId', false);
				$server = GD_Model_ServersMapper::get($server_id);

				$input_to_rev = $this->_request->getParam('toRevision', false);
				$comment = $this->_request->getParam('comment', '');

				try
				{
					$deploy_service = GD_Service_DeployFactory::factoryFromModels($user, $server, $project);
					$deployment = $deploy_service->createDeployment($input_to_rev, $comment);

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

			if ($this->_getParam("to"))
			{
				$data["toRevision"] = $this->_getParam("to");
			}

			if(count($data) > 0)
			{
				$form->populate($data);
			}
		}
	}

	private function getDeployServiceFromParam()
	{
		$deployment = GD_Model_DeploymentsMapper::get($this->_getParam('id'));

		$deploy_service = GD_Service_DeployFactory::factoryFromDeployment($deployment);
		return $deploy_service;
	}

	private function commonSetup()
	{
		$this->_helper->viewRenderer('main');
		$this->view->headLink()->appendStylesheet("/css/template/table.css");
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/deploy.css");

		$deploy_service = $this->getDeployServiceFromParam();
		$project = $deploy_service->getProject();
		$deployment = $deploy_service->getDeployment();

		$this->view->deployment = $deployment;
		$this->view->project = $project;
		$this->view->file_list = $deployment->getDeploymentFiles();

		$git = GD_Git::FromProject($project);
		$this->view->commit_log = $git->getCommitsBetween($deployment->getFromRevision(), $deployment->getToRevision());
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

		$this->commonSetup();

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
		$this->commonSetup();

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

		$this->commonSetup();

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
		$deployment = GD_Model_DeploymentsMapper::get($this->_getParam('id'));
		$deploy_service = GD_Service_DeployFactory::factoryFromDeployment($deployment);

		$data = $deploy_service->getDeploymentStatus();

		// Output stuff
		$this->_response->setHeader('Content-type', 'application/json');
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

		$deployment = GD_Model_DeploymentsMapper::get($this->_getParam('id'));
		$deploy_service = GD_Service_DeployFactory::factoryFromDeployment($deployment);

		$deploy_service->runDeployment();

		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	}

	public function getLastDeploymentRevisionAction()
	{
		$server = GD_Model_ServersMapper::get($this->_getParam("server_id"));

		$project = $this->_helper->getProjectFromUrl();

		$user = GD_Auth_Database::GetLoggedInUser();

		$deploy_service = GD_Service_DeployFactory::factoryFromModels($user, $server, $project);
		$from_rev = $deploy_service->getLastDeployedRevision();

		$data = array(
			'fromRevision' => $from_rev,
		);

		$this->_response->setHeader('Content-type', 'application/json');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$jsonData = Zend_Json::encode($data);
		$this->_response->appendBody($jsonData);
	}

	public function getLatestRevisionAction()
	{
		// Get project ID from url
		$project = $this->_helper->getProjectFromUrl();

		// Git pull before anything
		$git = GD_Git::FromProject($project);
		$git->gitPull();

		$data = array();

		$last_commit = $git->getLastCommit();
		if(is_array($last_commit))
		{
			$data['toRevision'] = $last_commit['HASH'];

			if(GD_Config::get("autofill_comments") == '1')
			{
				$data['autoComment'] = $last_commit['MESSAGE'];
			}
			else
			{
				$data['autoComment'] = '';
			}
		}

		$this->_response->setHeader('Content-type', 'application/json');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$jsonData = Zend_Json::encode($data);
		$this->_response->appendBody($jsonData);
	}

	public function apiAction()
	{
		$payload = $this->_helper->api();

		$server_id = (int)$payload['server'];

		if ($server_id <= 0)
		{
			return $this->_helper->api->respond(400, "Server ID '{$server_id}' was invalid");
		}

		GD_Debug::Log("API triggered deployment to commit '{$payload['to']}' on server {$server_id}", GD_Debug::DEBUG_FULL);

		$user = new GD_Model_User();
		$user->setId(0);

		$server = GD_Model_ServersMapper::get($server_id);

		$project = $this->_helper->getProjectFromUrl();

		$deploy_service = GD_Service_DeployFactory::factoryFromModels($user, $server, $project);
		$deploy_service->createDeployment("latest", $payload['comment']);
		$result = $deploy_service->runDeployment();

		if ($result)
		{
			return $this->_helper->api->respond(200);
		}
		else
		{
			return $this->_helper->api->respond(500);
		}
	}
}

