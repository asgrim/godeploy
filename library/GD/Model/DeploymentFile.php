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
class GD_Model_DeploymentFile
{
	protected $_id;
	protected $_deployments_id;
	protected $_deployment_file_actions_id;
	protected $_deployment_file_statuses_id;
	protected $_details;

	protected $_deployment_file_action;
	protected $_deployment_file_status;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setDeploymentsId($value)
	{
		$this->_deployments_id = (int)$value;
		return $this;
	}

	public function getDeploymentsId()
	{
		return $this->_deployments_id;
	}

	public function setDeploymentFileActionsId($value)
	{
		$this->_deployment_file_actions_id = (int)$value;
		return $this;
	}

	public function getDeploymentFileActionsId()
	{
		return $this->_deployment_file_actions_id;
	}

	public function setDeploymentFileStatusesId($value)
	{
		$this->_deployment_file_statuses_id = (int)$value;
		return $this;
	}

	public function getDeploymentFileStatusesId()
	{
		return $this->_deployment_file_statuses_id;
	}

	public function setDetails($value)
	{
		$this->_details = (string)$value;
		return $this;
	}

	public function getDetails()
	{
		return $this->_details;
	}

	public function setDeploymentFileAction(GD_Model_DeploymentFileAction $obj)
	{
		$this->_deployment_file_action = $obj;
		return $this;
	}

	public function getDeploymentFileAction()
	{
		return $this->_deployment_file_action;
	}

	public function setDeploymentFileStatus(GD_Model_DeploymentFileStatus $obj)
	{
		$this->_deployment_file_status = $obj;
		return $this;
	}

	public function getDeploymentFileStatus()
	{
		return $this->_deployment_file_status;
	}
}
