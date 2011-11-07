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
 * Map the configs_servers
 * @author james
 *
 */
class GD_Model_ConfigsServersMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_ConfigsServers";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_ConfigServer";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_ConfigServer $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'servers_id' => $obj->getServersId(),
			'configs_id' => $obj->getConfigsId(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_ConfigServer $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row)
	{
		$obj->setId($row->id)
			->setServersId($row->servers_id)
			->setConfigsId($row->configs_id);

		$s_map = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();
		$s_map->populateObjectFromRow($server, $row->findParentRow('GD_Model_DbTable_Servers'));
		$obj->setServer($server);

		$c_map = new GD_Model_ConfigsMapper();
		$config = new GD_Model_Config();
		$c_map->populateObjectFromRow($config, $row->findParentRow('GD_Model_DbTable_Configs'));
		$obj->setConfig($config);
	}

	/**
	 * Get a list of the servers for a configuration file
	 * @param int $config_id
	 * @return array of GD_Model_Server objects
	 */
	public function getAllServersForConfig($id)
	{
		$select = $this->getDbTable()
			->select()
			->where("configs_id = ?", $id);

		return $this->fetchAll($select);
	}

	/**
	 * Delete all servers for a configuration file
	 * @param int $id
	 * @return boolean
	 */
	public function deleteAllServersForConfig($id)
	{
		$id = (int)$id;

		if($id > 0)
		{
			return $this->getDbTable()->delete(array('configs_id = ?' => $id));
		}
		else
		{
			return false;
		}
	}
}
