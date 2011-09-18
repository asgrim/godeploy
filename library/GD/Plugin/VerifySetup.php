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
 * @author James Titcumb, Jon Wigham, Simon Wade
 * @link http://www.godeploy.com/
 */
class GD_Plugin_VerifySetup extends Zend_Controller_Plugin_Abstract
{
	private $_config;

	/**
	 * @param Zend_Config_Ini|null $config Config object
	 */
	public function __construct($config = null)
	{
		$this->_config = $config;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if(is_null($this->_config) && $request->controller != "setup")
		{
			$this->startInitialSetup();
		}
		else if($this->_config instanceof Zend_Config && $this->_config->setupComplete && $request->controller == "setup")
		{
			throw new GD_Exception("Setup controller disabled when setupComplete set to true.");
		}
		else if($this->_config instanceof Zend_Config && $this->_config->setupComplete && $request->controller != "setup")
		{
			// Check we're using the correct database version - throw exception if not
			$this->checkDatabaseVersion();
		}
	}

	protected function startInitialSetup()
	{
		$this->_response->setRedirect('/setup');
		$this->_response->sendResponse();
	}

	protected function checkDatabaseVersion()
	{
		$version_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/version.ini', 'version');
		$expected_db_version = (int)$version_conf->gd->expect_db_version;

		$db = Zend_Db_Table::getDefaultAdapter();

		try
		{
			$select = $db->select()
				->from('configuration', 'value')
				->where('`key` = ?', 'db_version');
		}
		catch(Zend_Db_Adapter_Exception $ex)
		{
			if($ex->getCode() == 1049)
			{
				$db_config = $db->getConfig();
				throw new GD_Exception("Database '{$db_config['dbname']}' was not created. Please make it...");
				//$db->getConnection()->exec('CREATE DATABASE `' . $db_config['dbname'] . '`'); // This doesn't work...
			}
			else
			{
				throw $ex;
			}
		}

		try
		{
			$current_db_version = (int)$db->fetchOne($select);
		}
		catch(Zend_Db_Statement_Exception $ex)
		{
			if($ex->getCode() == 42)
			{
				throw new GD_Exception("Please run the db/db_create_v{$expected_db_version}.sql script to initialise the database.");
			}
			else
			{
				throw $ex;
			}
		}

		if($current_db_version < $expected_db_version)
		{
			throw new GD_Exception("Database version was out of date. Expected '{$expected_db_version}' and it is currently at '{$current_db_version}'.");
		}
		else if($current_db_version > $expected_db_version)
		{
			throw new GD_Exception("Database version was too new??? Expected '{$expected_db_version}' and it is currently at '{$current_db_version}'.");
		}
	}

}