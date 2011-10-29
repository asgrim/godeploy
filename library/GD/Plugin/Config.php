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
 * Load version, database settings and language configurations
 *
 * @author james
 */
class GD_Plugin_Config extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var string Path to the generated config.ini
	 */
	private $_config_ini;

	/**
	 * Initialise variables
	 */
	public function __construct()
	{
		$this->_config_ini = APPLICATION_PATH . '/configs/config.ini';
	}

	/**
	 * Load version, database settings and language configurations
	 *
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// If a language is in session (from setup), set that first
		// May get overridden later in this file
		$use_lang = false;
		if(isset($setup_session->language))
		{
			$use_lang = $setup_session->language;
		}

		// Load version
		$version_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/version.ini', 'version');
		Zend_Registry::set("gd.version", $version_conf->gd->version);

		// Set default database adapter
		if(file_exists($this->_config_ini))
		{
			$db_conf = new Zend_Config_Ini($this->_config_ini, 'database');
			$adapter = Zend_Db::factory($db_conf->adapter, $db_conf->toArray());
			Zend_Db_Table::setDefaultAdapter($adapter);
			Zend_Registry::set("db", $db_conf);

			// If we're not on the /error/database page, do a DB test, else
			// we return out to ensure no DB errors later in this Bootstrap fn.
			if($request->controller != "error")
			{
				try
				{
					$adapter->query("SELECT 1");

					// If we can get the language from the database, use that language
					$lang = GD_Config::get("language");
					if($lang !== false)
					{
						$use_lang = $lang;
					}
				}
				catch(Exception $ex)
				{
					$this->_response->setRedirect('/error/database');
					$this->_response->sendResponse();
				}
			}

		}

		// If we can't set a language at all, default to english
		if(!$use_lang)
		{
			$use_lang = "english";
		}

		// Initialise translations now we should have a language to use
		$translate = GD_Translate::init($use_lang);
	}
}