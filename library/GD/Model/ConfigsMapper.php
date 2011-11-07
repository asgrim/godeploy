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
 * Map the configs table
 * @author james
 *
 */
class GD_Model_ConfigsMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_Configs";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_Config";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_Config $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'projects_id' => $obj->getProjectsId(),
			'date_added' => $obj->getDateAdded(),
			'added_users_id' => $obj->getAddedUsersId(),
			'date_updated' => date('Y-m-d H:i:s'),
			'updated_users_id' => $obj->getUpdatedUsersId(),
			'filename' => $obj->getFilename(),
			'content' => $obj->getContent(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_Config $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row)
	{
		$obj->setId($row->id)
			->setProjectsId($row->projects_id)
			->setDateAdded($row->date_added)
			->setAddedUsersId($row->added_users_id)
			->setDateUpdated($row->date_updated)
			->setUpdatedUsersId($row->updated_users_id)
			->setFilename($row->filename)
			->setContent($row->content);

		$p_map = new GD_Model_ProjectsMapper();
		$project = new GD_Model_Project();
		$p_map->populateObjectFromRow($project, $row->findParentRow('GD_Model_DbTable_Projects'));
		$obj->setProject($project);

		$u_map = new GD_Model_UsersMapper();
		$added_user = new GD_Model_User();
		$u_map->populateObjectFromRow($added_user, $row->findParentRow('GD_Model_DbTable_Users', 'GD_Model_DbTable_Users+Added'));

		$updated_user = new GD_Model_User();
		$u_map->populateObjectFromRow($updated_user, $row->findParentRow('GD_Model_DbTable_Users', 'GD_Model_DbTable_Users+Updated'));
		$obj->setUpdatedUser($updated_user);
	}

	/**
	 * Get a list of the configs for a project
	 * @param int $project_id
	 * @return array of GD_Model_Config objects
	 */
	public function getConfigsByProject($project_id)
	{
		$select = $this->getDbTable()
			->select()
			->where("projects_id = ?", $project_id)
			->order("filename ASC");

		return $this->fetchAll($select);
	}
}
