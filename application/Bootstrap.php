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
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('GD_');
	}

	protected function _initConfig()
	{
		$db_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/db.ini', 'database');
		Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($db_conf->adapter, $db_conf->toArray()));
	}

	protected function _initDatabaseVersion()
	{
		$conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$expected_db_version = (int)$conf->gd->db->version;

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

	protected function _initNavigation()
	{
		$auth = Zend_Auth::getInstance();

		if($auth->hasIdentity())
		{
			$navMode = 'navAccount';
			$logged_in = true;
		}
		else
		{
			$navMode = 'navLogin';
			$logged_in = false;
		}

		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', $navMode);

		$navigation = new Zend_Navigation($config);
		$view->navigation($navigation);
		$view->logged_in = $logged_in;
	}

	protected function _initAcl()
	{
		$auth = Zend_Auth::getInstance();
		$acl = new GD_Acl($auth);

		$frontController = $this->getResource('frontcontroller');
		$frontController->registerPlugin(new GD_Plugin_Auth($auth, $acl));
	}

	protected function _initRoutes()
	{
		$frontController = $this->getResource('frontcontroller');
		$router = $frontController->getRouter();

		$defaultRoute = new Zend_Controller_Router_Route(
			':controller/:action',
			array(
				'module'=>'default',
				'controller'=>'index',
				'action'=>'index',
			)
		);
		$projectRoute = new Zend_Controller_Router_Route(
			'project/:project/:controller/:action',
			array(
				'module'=>'default',
				'controller'=>'index',
				'action'=>'index',
				'project'=>'',
			),
			array(
				'project' => '[a-z-]+',
			)
		);
		$projectRoute = new Zend_Controller_Router_Route(
			'project/:project/:controller/:action/:id',
			array(
				'module'=>'default',
				'controller'=>'index',
				'action'=>'index',
				'project'=>'',
				'id' => 0,
			),
			array(
				'project' => '[a-z-]+',
				'id' => '\d+',
			)
		);

		$router->addRoute('default', $defaultRoute);
		$router->addRoute('project', $projectRoute);
	}

	protected function _initViewHelpers()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();

		$view->doctype('HTML5');

		// Meta tags
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');

		// Add default css file
		$view->headLink()->appendStylesheet("/css/template/common.css");
		$view->headLink()->appendStylesheet("/css/template/main.css");
		$view->headLink()->appendStylesheet("/css/template/header.css");
		$view->headLink()->appendStylesheet("/css/template/footer.css");
		$view->headLink()->appendStylesheet("/css/template/wrappers.css");

		$view->headScript()->appendFile("/js/prototype/1.7.js");
		$view->headScript()->appendFile("/js/scriptaculous/1.9.0.js");
		$view->headScript()->appendFile("/js/common.js");
	}
}

