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
class GD_Model_Config
{
	protected $_servers_id;
	protected $_configs_id;

	protected $_server;
	protected $_config;

	public function setServersId($value)
	{
		$this->_servers_id = (int)$value;
		return $this;
	}

	public function getServersId()
	{
		return $this->_servers_id;
	}

	public function setConfigsId($value)
	{
		$this->_configs_id = (int)$value;
		return $this;
	}

	public function getConfigsId()
	{
		return $this->_configs_id;
	}

	public function setServer(GD_Model_Server $obj)
	{
		$this->_server = $obj;
		return $this;
	}

	public function getServer()
	{
		return $this->_server;
	}

	public function setConfig(GD_Model_Config $obj)
	{
		$this->_config = $obj;
		return $this;
	}

	public function getConfig()
	{
		return $this->_config;
	}
}
