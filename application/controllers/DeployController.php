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

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/js/pages/deploy_setup.js");

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

		$form = new GDApp_Form_DeploymentSetup($project->getId());
		$this->view->form = $form;

		$deployments = new GD_Model_DeploymentsMapper();

		if($this->getRequest()->isPost())
		{
			$user = GD_Auth_Database::GetLoggedInUser();

			$deployment = new GD_Model_Deployment();
			$deployment->setUsersId($user->getId())
					->setProjectsId($project->getId())
					->setWhen(date("Y-m-d H:i:s"))
					->setServersId($this->_request->getParam('serverId', false))
					->setFromRevision($this->_request->getParam('fromRevision', false))
					->setToRevision($this->_request->getParam('toRevision', false))
					->setDeploymentStatusesId(1);

			$deployments->save($deployment);

			// Forward to either run or preview page...
			if(!is_null($this->_request->getParam('submitRun')))
			{
				// go to run
				$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/run/" . $deployment->getId());
			}
			else if(!is_null($this->_request->getParam('submitPreview')))
			{
				// go to preview
				$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/deploy/preview/" . $deployment->getId());
			}
		}
		else
		{
			$data = array();

			// Git pull before anything
			$git = new GD_Git($project);
			$git->gitPull();

			$last_commit = $git->getLastCommit();
			if(is_array($last_commit))
			{
				$to_revision = $last_commit['HASH'];
				$data['toRevision'] = $to_revision;
			}

			if(count($data) > 0)
			{
				$form->populate($data);
			}
		}
	}

	public function previewAction()
	{
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

		// TODO - This needs to be moved to the index action after form is submitted to populate the deployment_files table
		// Then this action needs to just pull data from that table.
		$git = new GD_Git($project);
		$files_changed = $git->getFilesChangedList($deployment->getFromRevision(), $deployment->getToRevision());
		$this->view->files_changed = $files_changed;
	}

	public function runAction()
	{
		die("Not done yet...");
	}

	public function resultAction()
	{
		die("Not done yet...");
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
}

