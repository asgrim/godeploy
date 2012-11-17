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
class GD_Model_Config
{
	protected $_id;
	protected $_projects_id;
	protected $_date_added;
	protected $_added_users_id;
	protected $_date_updated;
	protected $_updated_users_id;
	protected $_filename;
	protected $_content;

	protected $_project;
	protected $_added_user;
	protected $_updated_user;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
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

	public function setDateAdded($value)
	{
		$this->_date_added = strtotime($value);
		return $this;
	}

	public function getDateAdded($format = 'Y-m-d H:i:s')
	{
		return date($format, $this->_date_added);
	}

	public function setAddedUsersId($value)
	{
		$this->_added_users_id = (int)$value;
		return $this;
	}

	public function getAddedUsersId()
	{
		return $this->_added_users_id;
	}

	public function setDateUpdated($value)
	{
		$this->_date_updated = strtotime($value);
		return $this;
	}

	public function getDateUpdated($format = 'Y-m-d H:i:s')
	{
		return date($format, $this->_date_updated);
	}

	public function setUpdatedUsersId($value)
	{
		$this->_updated_users_id = (int)$value;
		return $this;
	}

	public function getUpdatedUsersId()
	{
		return $this->_updated_users_id;
	}

	public function setFilename($value)
	{
		$this->_filename = (string)$value;
		return $this;
	}

	public function getFilename()
	{
		return $this->_filename;
	}

	public function setContent($value)
	{
		$this->_content = (string)$value;
		return $this;
	}

	public function getContent()
	{
		return $this->_content;
	}

	public function getProcessedConfig(GD_Service_Deploy $deploy_service)
	{
		$deployment = $deploy_service->getDeployment();
		$server = $deploy_service->getServer();

		$config_content = $this->getContent();
		$config_content = str_replace("{{FROM_REV_LONG}}", $deployment->getFromRevision(), $config_content);
		$config_content = str_replace("{{FROM_REV_SHORT}}", substr($deployment->getFromRevision(), 0, 7), $config_content);
		$config_content = str_replace("{{TO_REV_LONG}}", $deployment->getToRevision(), $config_content);
		$config_content = str_replace("{{TO_REV_SHORT}}", substr($deployment->getToRevision(), 0, 7), $config_content);
		$config_content = str_replace("{{DATE}}", $deployment->getWhen(), $config_content);
		$config_content = str_replace("{{SERVER}}", $server->getHostname(), $config_content);
		$config_content = str_replace("{{USER}}", $deployment->getUser()->getName(), $config_content);
		$config_content = str_replace("{{COMMENT}}", $deployment->getComment(), $config_content);

		return $config_content;
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

	public function setAddedUser(GD_Model_User $obj)
	{
		$this->_added_user = $obj;
		return $this;
	}

	public function getAddedUser()
	{
		return $this->_added_user;
	}

	public function setUpdatedUser(GD_Model_User $obj)
	{
		$this->_updated_user = $obj;
		return $this;
	}

	public function getUpdatedUser()
	{
		return $this->_updated_user;
	}
}
