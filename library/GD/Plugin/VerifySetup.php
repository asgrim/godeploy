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
 * This controller plugin automatically verifies the app is setup and the
 * database is the correct version we are expecting
 *
 * @author james
 */
class GD_Plugin_VerifySetup extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Check if setup is complete. If not, start setup. If it is, check the
	 * config and database version
	 *
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// If we are on the error controller, return immediately to prevent
		// any database errors happening on error page
		if($request->controller == "error") return;

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

	/**
	 * Set a session variable to allow setup process to begin, then redirect to
	 * the setup controller to create a config
	 */
	protected function startInitialSetup()
	{
		$setup_session = new Zend_Session_Namespace('gd_setup_session');
		$setup_session->complete = false;

		$this->_response->setRedirect('/setup');
		$this->_response->sendResponse();
	}

	/**
	 * Create "secondary config" values if they don't exist
	 * Passing "true" as last arg to GD_Config::set means it won't overwrite
	 * a value if it's already there, so this is ideal for just checking we
	 * have any extra config settings
	 */
	protected function checkConfig()
	{
		GD_Config::set("debug_level", "0", true);
		GD_Config::set("logfile", sys_get_temp_dir() . "/godeploy_log", true);
		GD_Config::set("autofill_comments", "0", true); // Default this to off to not impact existing upgraded installations
		GD_Config::set("require_comments", "0", true); // Default this to off to not impact existing upgraded installations
		GD_Config::set("rows_per_history_page", "20", true);
	}

	/**
	 * Check the database version is the version we are expecting. If it isn't,
	 * attempt to automatically upgrade the database.
	 */
	protected function checkDatabaseVersion()
	{
		$version_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/version.ini', 'version');
		$expected_db_version = (int)$version_conf->gd->expect_db_version;

		try
		{
			$current_db_version = (int)GD_Config::get("db_version");

			if($current_db_version < $expected_db_version)
			{
				$session = new Zend_Session_Namespace('gd_session');
				if($session->upgrade_attempts >= 1)
				{
					$session->upgrade_attempts = 0;
					die("Database upgrade failed.");
				}
				else
				{
					$cfg = Zend_Db_Table::getDefaultAdapter()->getConfig();
					$db_adm = new GD_Db_Admin($cfg["host"], $cfg["username"], $cfg["password"], $cfg["dbname"]);
					$db_adm->upgradeDatabase($current_db_version, $expected_db_version);

					if(isset($session->upgrade_attempts))
					{
						$session->upgrade_attempts++;
					}
					else
					{
						$session->upgrade_attempts = 1;
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
