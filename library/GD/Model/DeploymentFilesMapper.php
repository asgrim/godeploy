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
 * Map the deployment files table
 * @author james
 *
 */
class GD_Model_DeploymentFilesMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_DeploymentFiles";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_DeploymentFile";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_DeploymentFile $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'deployments_id' => $obj->getDeploymentsId(),
			'deployment_file_actions_id' => $obj->getDeploymentFileActionsId(),
			'deployment_file_statuses_id' => $obj->getDeploymentFileStatusesId(),
			'details' => $obj->getDetails(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_DeploymentFile $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row)
	{
		$obj->setId($row->id)
			->setDeploymentsId($row->deployments_id)
			->setDeploymentFileActionsId($row->deployment_file_actions_id)
			->setDeploymentFileStatusesId($row->deployment_file_statuses_id)
			->setDetails($row->details);

		$dfa_map = new GD_Model_DeploymentFileActionsMapper();
		$deployment_file_action = new GD_Model_DeploymentFileAction();
		$dfa_map->populateObjectFromRow($deployment_file_action, $row->findParentRow('GD_Model_DbTable_DeploymentFileActions'));
		$obj->setDeploymentFileAction($deployment_file_action);

		$dfs_map = new GD_Model_DeploymentFileStatusesMapper();
		$deployment_file_status = new GD_Model_DeploymentFileStatus();
		$dfs_map->populateObjectFromRow($deployment_file_status, $row->findParentRow('GD_Model_DbTable_DeploymentFileStatuses'));
		$obj->setDeploymentFileStatus($deployment_file_status);
	}

	/**
	 * Get a list of the files for a deployment
	 * @param int $deployment_id
	 * @return array of GD_Model_DeploymentFile objects
	 */
	public function getDeploymentFilesByDeployment($deployment_id)
	{
		$select = $this->getDbTable()
			->select()
			->where("deployments_id = ?", $deployment_id);

		return $this->fetchAll($select);
	}
}
