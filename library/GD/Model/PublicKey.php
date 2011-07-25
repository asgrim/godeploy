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
class GD_Model_PublicKey
{
	protected $_id;
	protected $_public_key_types_id;
	protected $_data;
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

	public function setPublicKeyTypesId($id)
	{
		$this->_public_key_types_id = (int)$id;
		return $this;
	}

	public function getPublicKeyTypesId()
	{
		return $this->_public_key_types_id;
	}

	public function setData($value)
	{
		$this->_data = (string)$value;
		return $this;
	}

	public function getData()
	{
		return $this->_data;
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
