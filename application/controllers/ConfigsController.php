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

		$configs = new GD_Model_ConfigsMapper();
		$config = new GD_Model_Config();
		$config_id = $this->_getParam("id");

		$user = GD_Auth_Database::GetLoggedInUser();

		if($config_id > 0)
		{
			$configs->find($config_id, $config);
		}
		else
		{
			$config->setProjectsId($project->getId());
			$config->setDateAdded(date("Y-m-d H:i:s"));
			$config->setAddedUsersId($user->getId());
		}

		if($this->getRequest()->isPost())
		{
			$config->setFilename($this->_getParam("filename", ""));
			$config->setContent($this->_getParam("configContent", ""));
			$config->setUpdatedUsersId($user->getId());
			$configs->save($config);

			$this->_redirect($this->getFrontController()->getBaseUrl() . "/project/" . $this->_getParam("project") . "/configs");
		}
		else
		{
			$this->view->config = $config;
		}
	}
}
