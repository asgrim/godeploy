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
    		die("Not done yet... forward to either run or preview page...");
    	}
    	else
    	{
    		$last_deployment = $deployments->getLastSuccessfulDeployment();

    		if(!is_null($last_deployment))
    		{
				$data = array(
					'fromRevision' => $last_deployment->getToRevision(),
				);

	    		$form->populate($data);
    		}
    	}
    }

    public function previewAction()
    {
    	die("Not done yet...");
    }

    public function runAction()
    {
    	die("Not done yet...");
    }

    public function resultAction()
    {
    	die("Not done yet...");
    }
}

