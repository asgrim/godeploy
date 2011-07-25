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

/**
 * Map the projects table
 * @author james
 *
 */
class GD_Model_ServersMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_Servers";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_Server";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_Server $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'name' => $obj->getName(),
			'hostname' => $obj->getHostname(),
			'connection_types_id' => $obj->getConnectionTypesId(),
			'port' => $obj->getPort(),
			'username' => $obj->getUsername(),
			'password' => $obj->getPassword(),
			'remote_path' => $obj->getRemotePath(),
			'projects_id' => $obj->getProjectsId(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_Server $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row)
	{
		$obj->setId($row->id)
			->setName($row->name)
			->setHostname($row->hostname)
			->setConnectionTypesId($row->connection_types_id)
			->setPort($row->port)
			->setUsername($row->username)
			->setPassword($row->password)
			->setRemotePath($row->remote_path)
			->setProjectsId($row->projects_id);
	}

	/**
	 * Get a list of the servers for a project
	 * @param int $project_id
	 * @return array of GD_Model_Server objects
	 */
	public function getServersByProject($project_id)
	{
		$select = $this->getDbTable()
			->select()
			->where("projects_id = ?", $project_id);

		return $this->fetchAll($select);
	}
}
