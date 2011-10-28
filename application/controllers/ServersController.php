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
class ServersController extends Zend_Controller_Action
{
	private $_method;

	public function init()
	{
		$action = $this->_request->getActionName();

		if (!in_array($action . "Action", get_class_methods($this)))
		{
			if ($action == "add")
			{
				$this->_method = "add";
				$this->_forward("index");
			}
			else if ($action == "edit")
			{
				$this->_method = "edit";
				$this->_forward("index");
			}
			else if ($action == "confirm-delete")
			{
				$this->_method = "confirm-delete";
			}
		}
	}

	public function indexAction()
	{
    	$form = new GDApp_Form_ServerSettings();
    	$this->view->form = $form;

		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/project_servers.css");

		// Grab the project so we can add it to the title
    	$project_slug = $this->_getParam("project");
    	$projects = new GD_Model_ProjectsMapper();
   		$project = $projects->getProjectBySlug($project_slug);
    	$this->view->project = $project;

		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();

		$server_id = $this->_request->getParam('id', 0);

		if($server_id > 0 && $this->_method == "edit")
		{
			$this->view->headTitle('Edit Server');
			$servers->find($server_id, $server);
		}
		else if($this->_method == "add")
		{
			$this->view->headTitle('Add Server');
			$server->setProjectsId($project->getId());
    		$server->setName("New Server");
			$server->setPort(21);
		}
		$this->view->server = $server;

		if($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getParams()))
			{
				$server->setName($this->_request->getParam('name', false));
				$server->setHostname($this->_request->getParam('hostname', false));
				$server->setConnectionTypesId($this->_request->getParam('connectionTypeId', false));
				$server->setPort($this->_request->getParam('port', 21));
				$server->setUsername($this->_request->getParam('username', false));
				$server->setPassword($this->_request->getParam('password', false));
				$server->setRemotePath($this->_request->getParam('remotePath', false));

				if($server->getPort() <= 0)
				{
					$server->setPort(21);
				}

				// Test the connection first
				$ftp = new GD_Ftp($server);
				$result = $ftp->testConnection();

				if(!$result)
				{
					//throw new GD_Exception("Failed to test connection to FTP server.");
					$form->addError("FTP_CONNECT_ERROR");
					$this->view->ftpMessage = $ftp->getLastError();
				}
				else
				{
					$servers->save($server);

					$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/settings");
				}
			}
		}
		else
		{
			$data = array(
				'name' => $server->getName(),
				'hostname' => $server->getHostname(),
				'connectionTypeId' => $server->getConnectionTypesId(),
				'port' => $server->getPort(),
				'username' => $server->getUsername(),
				'password' => $server->getPassword(),
				'remotePath' => $server->getRemotePath(),
			);

    		$form->populate($data);
		}
	}

    public function confirmDeleteAction()
    {
    	$projects = new GD_Model_ProjectsMapper();
    	$project_slug = $this->_getParam("project");
    	$project = $projects->getProjectBySlug($project_slug);

		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();
		$server_id = $this->_request->getParam('id', 0);
		$servers->find($server_id, $server);

    	$this->view->project = $project;
    	$this->view->server = $server;

    	$this->view->headTitle('Confirm Server Delete');
		$this->view->headLink()->appendStylesheet("/css/template/table.css");
		$this->view->headLink()->appendStylesheet("/css/pages/confirm_delete.css");
    }

	public function deleteAction()
	{
		$project_slug = $this->_getParam("project");

		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();

		$server_id = $this->_request->getParam('id', 0);

		if($server_id > 0)
		{
			$servers->find($server_id, $server);
			$servers->delete($server);
			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $project_slug . "/settings");
		}
		else
		{
			throw new Zend_Exception("Server id was not specified");
		}
	}
}
