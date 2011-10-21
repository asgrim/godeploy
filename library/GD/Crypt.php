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
class GD_Crypt extends MAL_Crypt
{
	private $_key;

	public function __construct()
	{
		$raw_cryptkey = GD_Config::get("cryptkey");
		if(!isset($raw_cryptkey) || $raw_cryptkey == "")
		{
			throw new GD_Exception("The 'cryptkey' value must be specified in config.ini.");
		}
		$this->_key = md5($raw_cryptkey);
	}

	public function setKey($key)
	{
		$this->_key = $key;
	}

	public function doEncrypt($data)
	{
		return parent::Encrypt($data, $this->_key);
	}

	public function doDecrypt($data)
	{
		return parent::Decrypt($data, $this->_key);
	}

	public function makeHash($password)
	{
		return crypt($password, '$6$rounds=5000$' . substr(md5(microtime().rand()),0,16) . '$');
	}
}