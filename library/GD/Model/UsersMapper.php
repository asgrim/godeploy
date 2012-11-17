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
 * Map the users table
 * @author james
 *
 */
class GD_Model_UsersMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_Users";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_User";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_User $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'name' => $obj->getName(),
			'password' => $obj->getPassword(),
			'date_added' => $obj->getDateAdded(),
			'date_updated' => date('Y-m-d H:i:s'),
			'date_disabled' => $obj->getDateDisabled(),
			'admin' => $obj->getAdmin(),
			'active' => $obj->getActive(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_User $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row = null)
	{
		if (is_null($row))
		{
			$obj->setId(0)
				->setName("GoDeploy")
				->setActive(0)
				->setAdmin(0);
			return;
		}

		$obj->setId($row->id)
			->setName($row->name)
			->setPassword($row->password)
			->setDateAdded($row->date_added)
			->setDateUpdated($row->date_updated)
			->setDateDisabled($row->date_disabled)
			->setAdmin($row->admin)
			->setActive($row->active);
	}

	/**
	 * Search for a user by it's name
	 * @param string $name username to find
	 * @return GD_Model_User
	 */
	public function getUserByName($name, $only_active = false)
	{
		$obj = new GD_Model_User();

		$select = $this->getDbTable()
			->select()
			->where("name = ?", $name);

		if($only_active)
		{
			$select->where("active = 1");
		}

		$row = $this->getDbTable()->fetchRow($select);

		if(is_null($row))
		{
			return null;
		}
		$this->populateObjectFromRow($obj, $row);
		return $obj;
	}
}
