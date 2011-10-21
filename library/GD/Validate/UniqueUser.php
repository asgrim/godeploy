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
 * Validate that a user does not already exist in the database
 *
 * @author james
 */
class GD_Validate_UniqueUser extends Zend_Validate_Abstract
{
	const ISUNIQUE = 'isunique';

	protected $_messageTemplates = array(
		self::ISUNIQUE => "This username is not unique - please enter something different",
	);

	private $_current_user;

	/**
	 * @param int|false $current_user User ID of the current user
	 */
	public function __construct($current_user = false)
	{
		$this->_current_user = $current_user;
	}

	public function isValid($value)
	{
		$this->_setValue($value);

		if($this->_current_user == $value)
		{
			return true;
		}

		$m_users = new GD_Model_UsersMapper();
		$existing_user = $m_users->getUserByName($value, false);

		if (is_null($existing_user))
		{
			return true;
		}
		else
		{
			$this->_error(self::ISUNIQUE);
			return false;
		}
	}
}