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
class GD_Model_User
{
	protected $_id;
	protected $_name;
	protected $_password;
	protected $_date_added;
	protected $_date_updated;
	protected $_date_disabled;
	protected $_admin;
	protected $_active;

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
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setPassword($value)
	{
		$this->_password = (string)$value;
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
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

	public function setDateUpdated($value)
	{
		$this->_date_updated = strtotime($value);
		return $this;
	}

	public function getDateUpdated($format = 'Y-m-d H:i:s')
	{
		return date($format, $this->_date_updated);
	}

	public function setDateDisabled($value)
	{
		$this->_date_disabled = strtotime($value);
		return $this;
	}

	public function getDateDisabled($format = 'Y-m-d H:i:s')
	{
		return date($format, $this->_date_disabled);
	}

	public function setAdmin($value)
	{
		$value = (int)$value;
		$this->_admin = $value > 0 ? 1 : 0;
		return $this;
	}

	public function getAdmin()
	{
		return $this->_admin;
	}

	public function setActive($value)
	{
		$value = (int)$value;
		$this->_active = $value > 0 ? 1 : 0;
		return $this;
	}

	public function getActive()
	{
		return $this->_active;
	}

	public function disableUser()
	{
		$this->setActive(0);
		$this->setDateDisabled(date('Y-m-d H:i:s'));
		return $this;
	}

	public function enableUser()
	{
		$this->setActive(1);
		return $this;
	}

	public function isAdmin()
	{
		return ($this->getAdmin() == 1);
	}

	public function isActive()
	{
		return ($this->getActive() == 1);
	}
}
