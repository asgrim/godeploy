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
class GD_Model_Server
{
	protected $_id;
	protected $_name;
	protected $_hostname;
	protected $_connection_types_id;
	protected $_port;
	protected $_username;
	protected $_password;
	protected $_remote_path;
	protected $_projects_id;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setName($value)
	{
		$this->_name = (string)$value;
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setHostname($value)
	{
		$this->_hostname = (string)$value;
		return $this;
	}

	public function getHostname()
	{
		return $this->_hostname;
	}

	public function setConnectionTypesId($id)
	{
		$this->_connection_types_id = (int)$id;
		return $this;
	}

	public function getConnectionTypesId()
	{
		return $this->_connection_types_id;
	}

	public function setPort($value)
	{
		$this->_port = (int)$value;
		return $this;
	}

	public function getPort()
	{
		return $this->_port;
	}

	public function setUsername($value)
	{
		$this->_username = (string)$value;
		return $this;
	}

	public function getUsername()
	{
		return $this->_username;
	}

	public function setPassword($value)
	{
		$this->_password = (string)$value;
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setRemotePath($value)
	{
		$this->_remote_path = (string)$value;
		return $this;
	}

	public function getRemotePath()
	{
		return $this->_remote_path;
	}

	public function setProjectsId($id)
	{
		$this->_projects_id = (int)$id;
		return $this;
	}

	public function getProjectsId()
	{
		return $this->_projects_id;
	}
}
