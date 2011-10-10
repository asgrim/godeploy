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
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if((GD_Config::get("setup_complete") === false || GD_Config::get("setup_complete") != "1") && $request->controller != "setup")
		{
			$this->startInitialSetup();
		}
		else if(GD_Config::get("setup_complete") == "1" && $request->controller == "setup")
		{
			$this->_response->setRedirect('/');
			$this->_response->sendResponse();
		}
		else if(GD_Config::get("setup_complete") == "1" && $request->controller != "setup")
		{
			// Check we're using the correct database version - throw exception if not
			$this->checkDatabaseVersion();
			$this->checkConfig();
		}
	}

	protected function startInitialSetup()
	{
		$setup_session = new Zend_Session_Namespace('gd_setup_session');
		$setup_session->complete = false;

		$this->_response->setRedirect('/setup');
		$this->_response->sendResponse();
	}

	protected function checkConfig()
	{
		// Create "secondary config" values if they don't exist
		// Passing "true" as last arg to GD_Config::set means it won't overwrite
		// a value if it's already there, so this is ideal for just checking we
		// have any extra config settings
		GD_Config::set("debug_level", "0", true);
	}

	protected function checkDatabaseVersion()
	{
		$version_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/version.ini', 'version');
		$expected_db_version = (int)$version_conf->gd->expect_db_version;

		try
		{
			$current_db_version = (int)GD_Config::get("db_version");

			if($current_db_version < $expected_db_version)
			{
				if($_SESSION["UPGRADE_ATTEMPTS"] >= 1)
				{
					$_SESSION["UPGRADE_ATTEMPTS"] = 0;
					die("Database upgrade failed.");
				}
				else
				{
					$cfg = Zend_Db_Table::getDefaultAdapter()->getConfig();
					$db_adm = new GD_Db_Admin($cfg["host"], $cfg["username"], $cfg["password"], $cfg["dbname"]);
					$db_adm->upgradeDatabase($current_db_version, $expected_db_version);

					if(isset($_SESSION["UPGRADE_ATTEMPTS"]))
					{
						$_SESSION["UPGRADE_ATTEMPTS"]++;
					}
					else
					{
						$_SESSION["UPGRADE_ATTEMPTS"] = 1;
					}

					$this->_response->setRedirect('/');
					$this->_response->sendResponse();
				}
			}
			else if($current_db_version > $expected_db_version)
			{
				die("Database version was too new??? Expected '{$expected_db_version}' and it is currently at '{$current_db_version}'.");
			}
		}
		catch(Exception $ex)
		{
			$this->_request->setParam('db_error_detail', $ex->getMessage());
			$this->_request->setModuleName('default');
			$this->_request->setControllerName('error');
			$this->_request->setActionName('database');
			return;
		}
	}

}