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

/**
 * Map the connection types table
 * @author james
 *
 */
class GD_Model_DeploymentsMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_Deployments";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_Deployment";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_Deployment $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'users_id' => $obj->getUsersId(),
			'projects_id' => $obj->getProjectsId(),
			'when' => $obj->getWhen(),
			'servers_id' => $obj->getServersId(),
			'from_revision' => $obj->getFromRevision(),
			'to_revision' => $obj->getToRevision(),
			'comment' => $obj->getComment(),
			'deployment_statuses_id' => $obj->getDeploymentStatusesId(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_Deployment $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row)
	{
		$obj->setId($row->id)
			->setUsersId($row->users_id)
			->setProjectsId($row->projects_id)
			->setWhen($row->when)
			->setServersId($row->servers_id)
			->setFromRevision($row->from_revision)
			->setToRevision($row->to_revision)
			->setComment($row->comment)
			->setDeploymentStatusesId($row->deployment_statuses_id);

		$u_map = new GD_Model_UsersMapper();
		$user = new GD_Model_User();
		$u_map->populateObjectFromRow($user, $row->findParentRow('GD_Model_DbTable_Users'));
		$obj->setUser($user);

		$p_map = new GD_Model_ProjectsMapper();
		$project = new GD_Model_Project();
		$p_map->populateObjectFromRow($project, $row->findParentRow('GD_Model_DbTable_Projects'));
		$obj->setProject($project);

		$s_map = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();
		$s_map->populateObjectFromRow($server, $row->findParentRow('GD_Model_DbTable_Servers'));
		$obj->setServer($server);

		$ds_map = new GD_Model_DeploymentStatusesMapper();
		$deployment_status = new GD_Model_DeploymentStatus();
		$ds_map->populateObjectFromRow($deployment_status, $row->findParentRow('GD_Model_DbTable_DeploymentStatuses'));
		$obj->setDeploymentStatus($deployment_status);
	}

	/**
	 * Find the last successful deployment object
	 * @param int $project_id
	 * @param int $server_id
	 * @return GD_Model_Deployment
	 */
	public function getLastSuccessfulDeployment($project_id, $server_id = 0)
	{
		$obj = new GD_Model_Deployment();

		$select = $this->getDbTable()
			->select()
			->where("deployment_statuses_id = ?", 3)
			->where("projects_id = ?", $project_id);

		if($server_id > 0)
		{
			$select->where("servers_id = ?", $server_id);
		}

		$select->order('when DESC')
			->limit(1);

		$row = $this->getDbTable()->fetchRow($select);

		if(is_null($row))
		{
			return null;
		}
		$this->populateObjectFromRow($obj, $row);
		return $obj;
	}

	/**
	 * Get a list of the deployments for a project
	 * @param int $project_id
	 * @return array of GD_Model_Deployment objects
	 */
	public function getDeploymentsByProject($project_id, $offset = 0, $limit = 15, $include_previews = false)
	{
		$select = $this->getDbTable()
			->select()
			->where("projects_id = ?", $project_id)
			->order("when DESC")
			->limit($limit, $offset);

		if(!$include_previews)
		{
			$select->where("deployment_statuses_id != 1");
		}

		return $this->fetchAll($select);
	}

	/**
	 * Get a list of the deployments for a server
	 * @param int $server_id
	 * @return array of GD_Model_Deployment objects
	 */
	public function getDeploymentsByServer($server_id, $include_previews = false)
	{
		$select = $this->getDbTable()
			->select()
			->where("servers_id = ?", $server_id)
			->order("when DESC");

		if(!$include_previews)
		{
			$select->where("deployment_statuses_id != 1");
		}

		return $this->fetchAll($select);
	}

	/**
	 * @param int $num_deployments
	 */
	public function getNumDeployments($project_id)
	{
		$select = $this->getDbTable()->getDefaultAdapter()
			->select()
			->from("deployments", "COUNT(*)")
			->where("projects_id = ?", $project_id)
			->where("deployment_statuses_id != 1");

		return $this->getDbTable()->getDefaultAdapter()->fetchOne($select);
	}

	public static function get($deployment_id)
	{
		$deployment = new GD_Model_Deployment();
		$deployments = new self;
		$deployments->find((int)$deployment_id, $deployment);
		return $deployment;
	}
}
