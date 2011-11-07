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
class ConfigsController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->view->headTitle('Configuration Management');

		$this->view->headLink()->appendStylesheet("/css/template/table.css");

		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		$this->view->project = $project;

		$configs = new GD_Model_ConfigsMapper();
		$this->view->configs = $configs->getConfigsByProject($project->getId());

		$configs_servers_map = new GD_Model_ConfigsServersMapper();

		foreach($this->view->configs as $config)
		{
			$config->servers = $configs_servers_map->getAllServersForConfig($config->getId());
		}
	}

	public function editAction()
	{
		$this->view->headTitle('Configuration Management');
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/configs_edit.css");

		$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		$this->view->project = $project;

		$configs_map = new GD_Model_ConfigsMapper();
		$config = new GD_Model_Config();
		$config_id = $this->_getParam("id");

		$configs_servers_map = new GD_Model_ConfigsServersMapper();
		$config_server_ids = array();

		$servers_map = new GD_Model_ServersMapper();
		$servers = $servers_map->getServersByProject($project->getId());
		$this->view->servers = $servers;

		$user = GD_Auth_Database::GetLoggedInUser();

		if($config_id > 0)
		{
			$configs_map->find($config_id, $config);

			$config_servers = $configs_servers_map->getAllServersForConfig($config->getId());

			foreach($config_servers as $cs)
			{
				$config_server_ids[] = $cs->getServersId();
			}
		}
		else
		{
			$config->setProjectsId($project->getId());
			$config->setDateAdded(date("Y-m-d H:i:s"));
			$config->setAddedUsersId($user->getId());

			foreach($servers as $server)
			{
				$config_server_ids[] = $server->getId();
			}
		}

		$this->view->config_server_ids = $config_server_ids;

		if($this->getRequest()->isPost())
		{
			// First save the config file itself
			$config->setFilename($this->_getParam("filename", ""));
			$config->setContent($this->_getParam("configContent", ""));
			$config->setUpdatedUsersId($user->getId());
			$configs_map->save($config);

			// Then loop through the config_servers and update
			$configs_servers_map->deleteAllServersForConfig($config->getId());

			$add_to_servers = $this->_getParam("servers", array());

			foreach($add_to_servers as $server_id)
			{
				$cs = new GD_Model_ConfigServer();
				$cs->setConfigsId($config->getId());
				$cs->setServersId($server_id);
				$configs_servers_map->save($cs);
			}

			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/configs");
		}
		else
		{
			$this->view->config = $config;
		}
	}
}
