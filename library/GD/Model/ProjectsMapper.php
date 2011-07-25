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
class GD_Model_ProjectsMapper extends MAL_Model_MapperAbstract
{

	/**
	 * Should return the name of the table being used e.g. "Application_Model_DbTable_BlogPost"
	 * @return string
	 */
	protected function getDbTableName()
	{
		return "GD_Model_DbTable_Projects";
	}

	/**
	 * Should return the name of the object being used e.g. "Application_Model_BlogPost"
	 * @return string
	 */
	protected function getObjectName()
	{
		return "GD_Model_Project";
	}

	/**
	 * Should return an array of mapped fields to use in the MAL_Model_MapperAbstract::Save function
	 * @param GD_Model_Project $obj
	 */
	protected function getSaveData($obj)
	{
		$data = array(
			'name' => $obj->getName(),
			'slug' => $obj->getSlug(),
			'repository_types_id' => $obj->getRepositoryTypesId(),
			'repository_url' => $obj->getRepositoryUrl(),
			'deployment_branch' => $obj->getDeploymentBranch(),
			'public_keys_id' => $obj->getPublicKeysId(),
		);
		return $data;
	}

	/**
	 * Implement this by setting $obj values (e.g. $obj->setId($row->Id) from a DB row
	 * @param GD_Model_Project $obj
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	protected function populateObjectFromRow(&$obj, Zend_Db_Table_Row_Abstract $row)
	{
		$obj->setId($row->id)
			->setName($row->name)
			->setSlug($row->slug)
			->setRepositoryTypesId($row->repository_types_id)
			->setRepositoryUrl($row->repository_url)
			->setDeploymentBranch($row->deployment_branch)
			->setPublicKeysId($row->public_keys_id);

		$pk_map = new GD_Model_PublicKeysMapper();
		$public_key = new GD_Model_PublicKey();
		$pk_map->populateObjectFromRow($public_key, $row->findParentRow('GD_Model_DbTable_PublicKeys'));
		$obj->setPublicKey($public_key);
	}

	/**
	 * Search for a user by it's name
	 * @param string $name username to find
	 * @return GD_Model_Users
	 */
	public function getProjectBySlug($slug)
	{
		$obj = new GD_Model_Project();

		$select = $this->getDbTable()
			->select()
			->where("slug = ?", $slug);

		$row = $this->getDbTable()->fetchRow($select);

		if(is_null($row))
		{
			return null;
		}
		$this->populateObjectFromRow($obj, $row);
		return $obj;
	}
}
