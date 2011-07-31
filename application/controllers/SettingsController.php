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

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$form = new GDApp_Form_ProjectSettings();
    	$this->view->form = $form;

    	$projects = new GD_Model_ProjectsMapper();
    	$project_slug = $this->_getParam("project");
    	if($project_slug != "new")
    	{
    		$project = $projects->getProjectBySlug($project_slug);
    	}
    	else
    	{
    		$project = new GD_Model_Project();
    		$project->setName("New Project");
    		$project->setDeploymentBranch('master'); // Usually default for git
    	}
    	$this->view->project = $project;

    	if($this->getRequest()->isPost())
    	{
    		$project->setName($this->_request->getParam('name', false));
    		$project->setRepositoryUrl($this->_request->getParam('repositoryUrl', false));
    		$project->setDeploymentBranch($this->_request->getParam('deploymentBranch', false));
    		$project->setRepositoryTypesId(1);

    		// Save public key
    		$public_key = $project->getPublicKey();
    		$public_key->setData($this->_request->getParam('publicKey', false));
    		$public_keys = new GD_Model_PublicKeysMapper();
    		$public_keys->save($public_key);

    		$project->setPublicKeysId($public_key->getId());

    		$projects->save($project);

    		// Update repository
    		$git = new GD_Git($project);
    		$result = $git->gitCloneOrPull();
    		if($result !== true)
    		{
    			throw new GD_Exception("Git clone or pull failed: {$result}");
    		}

    		$this->_redirect($this->getFrontController()->getBaseUrl() . "/home");
    	}
    	else
    	{
    		$data = array(
				'name' => $project->getName(),
				'repositoryUrl' => $project->getRepositoryUrl(),
				'deploymentBranch' => $project->getDeploymentBranch(),
				'publicKey' => $project->getPublicKey()->getData(),
			);

    		$form->populate($data);

    		// Populate list of servers for this project
    		if($project->getId() > 0)
    		{
	    		$servers = new GD_Model_ServersMapper();
	    		$this->view->servers = $servers->getServersByProject($project->getId());
    		}
    	}
    }

    public function deleteAction()
    {
    	$projects = new GD_Model_ProjectsMapper();
    	$project_slug = $this->_getParam("project");

    	$project = $projects->getProjectBySlug($project_slug);

    	// Delete the public key
    	$public_key = $project->getPublicKey();
    	$public_keys = new GD_Model_PublicKeysMapper();
    	$public_keys->delete($public_key);

    	// Delete the project
    	$projects->delete($project);

    	$this->_redirect($this->getFrontController()->getBaseUrl() . "/home");
    }


}

