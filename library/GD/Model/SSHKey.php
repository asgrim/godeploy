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
class GD_Model_SSHKey
{
	protected $_id;
	protected $_ssh_key_types_id;
	protected $_private_key;
	protected $_public_key;
	protected $_comment;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setSSHKeyTypesId($id)
	{
		$this->_ssh_key_types_id = (int)$id;
		return $this;
	}

	public function getSSHKeyTypesId()
	{
		return $this->_ssh_key_types_id;
	}

	public function setPrivateKey($value)
	{
		$this->_private_key = (string)$value;
		return $this;
	}

	public function getPrivateKey()
	{
		return $this->_private_key;
	}

	public function setPublicKey($value)
	{
		$this->_public_key = (string)$value;
		return $this;
	}

	public function getPublicKey()
	{
		return $this->_public_key;
	}

	public function setComment($value)
	{
		$this->_comment = (string)$value;
		return $this;
	}

	public function getComment()
	{
		return $this->_comment;
	}
}
