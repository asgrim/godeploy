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

class GD_Config
{
	/**
	 * @var Zend_Db_Adapter_Abstract private instance of a Zend_Db_Adapter
	 */
	private static $_db;

	private static $_db_table;

	private static function initialise()
	{
		if(!isset(self::$_db))
		{
			self::$_db_table = 'configuration';
			self::$_db = Zend_Db_Table::getDefaultAdapter();

			if(!self::$_db)
			{
				return false;
			}
		}
		return true;
	}

	public static function get($key)
	{
		if(!self::initialise()) return false;

		$select = self::$_db->select()
				->from(self::$_db_table, 'value')
				->where('`key` = ?', $key);

		return self::$_db->fetchOne($select);
	}

	public static function set($key, $value, $dont_update = false)
	{
		if(!self::initialise()) return false;

		// Create data to bind to
		$data = array(
			'key' => $key,
			'value' => $value,
		);

		// Try to get value first
		$existing_val = self::get($key);

		if($existing_val === false)
		{
			self::$_db->insert(self::$_db_table, $data);
			return self::$_db->lastInsertId();
		}
		else if(!$dont_update)
		{
			$where['key = ?']  = $key;
			return self::$_db->update(self::$_db_table, $data, $where) == 1;
		}
		else if($dont_update)
		{
			return true;
		}

		return false;
	}
}