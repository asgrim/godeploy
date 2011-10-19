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
class SettingsController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/template/table.css");
		$this->view->headLink()->appendStylesheet("/css/pages/project_settings.css");

		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if ($project_slug != "new")
		{
			$this->view->headTitle('Edit Project');
			$project = $projects->getProjectBySlug($project_slug);
			$new_project = false;
		}
		else
		{
			$this->view->headTitle('Add Project');
			$project = new GD_Model_Project();
			$project->setName("New Project");
			$project->setDeploymentBranch('master'); // Usually default for git

			// Load the ssh key
			$ssh_keys_map = new GD_Model_SSHKeysMapper();
			$ssh_keys = new GD_Model_SSHKey();
			$ssh_keys_map->find(1, $ssh_keys);
			$project->setSSHKey($ssh_keys);

			$new_project = true;
		}
		$this->view->project = $project;

		// Populate list of servers for this project
		if ($project->getId() > 0)
		{
			$servers = new GD_Model_ServersMapper();
			$this->view->servers = $servers->getServersByProject($project->getId());
		}

		$form = new GDApp_Form_ProjectSettings($project, null, $new_project);
		$this->view->form = $form;
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getParams()))
			{
				$result = $this->saveProject($projects, $project, $new_project, ($this->_getParam("errored") == "true"));
				if($result !== true)
				{
					$form->repositoryUrl->addError("Could not clone the git repository [" . $result . "]. Please check the URL is correct and try again.");
				}
				else
				{
					$this->_redirect($this->getFrontController()->getBaseUrl() . "project/" . $project->getSlug() . '/settings');
				}
			}
		}
		else
		{
			if(!$new_project)
			{
				$git = new GD_Git($project);

				try
				{
					$git->checkValidRepository();
					$this->view->valid_repository = true;
				}
				catch(GD_Exception $ex)
				{
					$this->view->valid_repository = false;
					$form->repositoryUrl->addError("Check this git repository URL - is it correct?");
				}
			}

			$data = array(
				'name' => $project->getName(),
				'repositoryUrl' => $project->getRepositoryUrl(),
				'deploymentBranch' => $project->getDeploymentBranch(),
				'publicKey' => $project->getSSHKey()->getPublicKey(),
			);

			$form->populate($data);
		}
	}

	public function confirmDeleteAction()
	{
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		$project = $projects->getProjectBySlug($project_slug);

		$this->view->project = $project;

		$this->view->headTitle('Confirm Project Delete');
		$this->view->headLink()->appendStylesheet("/css/pages/confirm_delete.css");
	}

	public function deleteAction()
	{
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");

		$project = $projects->getProjectBySlug($project_slug);

		// Initialise the mapper objects we'll need
		$deploymentsMapper = new GD_Model_DeploymentsMapper();
		$deploymentFilesMapper = new GD_Model_DeploymentFilesMapper();
		$serversMapper = new GD_Model_ServersMapper();

		// Delete the deployments associated with the project.
		$deployments = $deploymentsMapper->getDeploymentsByProject($project->getId());
		foreach ($deployments as $deployment)
		{
			// Delete the files associated with the project.
			$deploymentFiles = $deploymentFilesMapper->getDeploymentFilesByDeployment($deployment->getId());
			foreach ($deploymentFiles as $deploymentFile)
			{
				$deploymentFilesMapper->delete($deploymentFile);
			}
			// Delete deployment.
			$deploymentsMapper->delete($deployment);
		}

		// Delete the servers associated with the project.
		$servers = $serversMapper->getServersByProject($project->getId());
		foreach ($servers as $server)
		{
			$serversMapper->delete($server);
		}

		// Delete the project's git repo.
		$git = new GD_Git($project);
		$git->deleteRepository();

		// Delete the project
		$projects->delete($project);
		$this->_redirect($this->getFrontController()->getBaseUrl() . "/home");
	}


	public function saveProject(GD_Model_ProjectsMapper $projects, GD_Model_Project $project, $new_project = false, $errored = false)
	{
		$repo_before = $project->getRepositoryUrl();
		$branch_before = $project->getDeploymentBranch();
		$project->setName($this->_request->getParam('name', false));
		$project->setRepositoryUrl($this->_request->getParam('repositoryUrl', false));
		$project->setDeploymentBranch($this->_request->getParam('deploymentBranch', false));
		$project->setRepositoryTypesId(1);
		$project->setSSHKeysId(1);
		$repo_after = $project->getRepositoryUrl();
		$branch_after = $project->getDeploymentBranch();

		// Save the project
		$projects->save($project);

		$git = new GD_Git($project);

		$branch_changed = false;

		// If repo URL changed, delete and re-clone
		if($repo_before != $repo_after || $new_project || $errored)
		{
			// Delete any existing repo
			$git->deleteRepository();

			// Clone repository from source
			$result = $git->gitClone();
			if($result !== true)
			{
				return $result;
			}

			// Checkout the appropriate branch
			if(!$git->gitCheckout($branch_after))
			{
				return "Branch '{$branch_after}' does not exist.";
			}
			$branch_changed = true;
		}

		if($branch_before != $branch_after && !$branch_changed)
		{
			// If not already checked out, or branch has changed, do a checkout
			$git->gitCheckout($branch_after);
		}

		return true;
	}

	public function recloneAction()
	{
		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");

		$project = $projects->getProjectBySlug($project_slug);

		$git = new GD_Git($project);

		$git->deleteRepository();

		$result = $git->gitClone();
		if($result !== true)
		{
			throw new GD_Exception("Reclone failed [$result].");
		}

		if($this->_getParam("return"))
		{
			$this->_redirect(base64_decode($this->_getParam("return")));
		}
		else
		{
			$this->_redirect("/project/{$project_slug}/settings");
		}
	}
}
