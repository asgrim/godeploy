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
class GD_Model_Project
{
	protected $_id;
	protected $_name;
	protected $_slug;
	protected $_repository_types_id;
	protected $_repository_url;
	protected $_deployment_branch;
	protected $_public_keys_id;

	protected $_public_key;

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

		$slug = strtolower($this->_name);
		$slug = preg_replace("/[^0-9a-z]/", "-", $slug);
		$this->setSlug($slug);

		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setSlug($value)
	{
		$this->_slug = (string)$value;
		return $this;
	}

	public function getSlug()
	{
		return $this->_slug;
	}

	public function setRepositoryTypesId($id)
	{
		$this->_repository_types_id = (int)$id;
		return $this;
	}

	public function getRepositoryTypesId()
	{
		return $this->_repository_types_id;
	}

	public function setRepositoryUrl($value)
	{
		$this->_repository_url = (string)$value;
		return $this;
	}

	public function getRepositoryUrl()
	{
		return $this->_repository_url;
	}

	public function setDeploymentBranch($value)
	{
		$this->_deployment_branch = (string)$value;
		return $this;
	}

	public function getDeploymentBranch()
	{
		return $this->_deployment_branch;
	}

	public function setPublicKeysId($id)
	{
		$this->_public_keys_id = (int)$id;
		return $this;
	}

	public function getPublicKeysId()
	{
		return $this->_public_keys_id;
	}

	public function setPublicKey(GD_Model_PublicKey $obj)
	{
		$this->_public_key = $obj;
		return $this;
	}

	/**
	 * @return GD_Model_PublicKeys
	 */
	public function getPublicKey()
	{
		return $this->_public_key;
	}

}
