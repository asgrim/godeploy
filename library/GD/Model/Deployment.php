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
class GD_Model_Deployment
{
	protected $_id;
	protected $_users_id;
	protected $_projects_id;
	protected $_when;
	protected $_servers_id;
	protected $_from_revision;
	protected $_to_revision;
	protected $_deployment_statuses_id;

	protected $_user;
	protected $_project;
	protected $_server;
	protected $_deployment_status;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setUsersId($value)
	{
		$this->_users_id = (int)$value;
		return $this;
	}

	public function getUsersId()
	{
		return $this->_users_id;
	}

	public function setProjectsId($value)
	{
		$this->_projects_id = (int)$value;
		return $this;
	}

	public function getProjectsId()
	{
		return $this->_projects_id;
	}

	public function setWhen($value)
	{
		$this->_when = strtotime($value);
		return $this;
	}

	public function getWhen($format = 'Y-m-d H:i:s')
	{
		return date($format, $this->_when);
	}

	public function setServersId($value)
	{
		$this->_servers_id = (int)$value;
		return $this;
	}

	public function getServersId()
	{
		return $this->_servers_id;
	}

	public function setFromRevision($value)
	{
		$this->_from_revision = (string)$value;
		return $this;
	}

	public function getFromRevision()
	{
		return $this->_from_revision;
	}

	public function setToRevision($value)
	{
		$this->_to_revision = (string)$value;
		return $this;
	}

	public function getToRevision()
	{
		return $this->_to_revision;
	}

	public function setDeploymentStatusesId($value)
	{
		$this->_deployment_statuses_id = (int)$value;
		return $this;
	}

	public function getDeploymentStatusesId()
	{
		return $this->_deployment_statuses_id;
	}

	public function setUser(GD_Model_User $obj)
	{
		$this->_user = $obj;
		return $this;
	}

	public function getUser()
	{
		return $this->_user;
	}

	public function setProject(GD_Model_Project $obj)
	{
		$this->_project = $obj;
		return $this;
	}

	public function getProject()
	{
		return $this->_project;
	}

	public function setServer(GD_Model_Server $obj)
	{
		$this->_server = $obj;
		return $this;
	}

	public function getServer()
	{
		return $this->_server;
	}

	public function setDeploymentStatus(GD_Model_DeploymentStatus $obj)
	{
		$this->_deployment_status = $obj;
		return $this;
	}

	public function getDeploymentStatus()
	{
		return $this->_deployment_status;
	}
}
