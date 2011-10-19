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
		// Default languages
		$use_lang = false;
		if(isset($setup_session->language))
		{
			$use_lang = $setup_session->language;
		}

		// Config file
		$cfg_file = APPLICATION_PATH . '/configs/config.ini';

		// Set default database adapter
		if(file_exists($cfg_file))
		{
			$db_conf = new Zend_Config_Ini($cfg_file, 'database');
			Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($db_conf->adapter, $db_conf->toArray()));
			Zend_Registry::set("db", $db_conf);

			$lang = GD_Config::get("language");
			if($lang !== false)
			{
				$use_lang = $lang;
			}
		}

		if(!$use_lang)
		{
			$use_lang = "english";
		}

		$translate = GD_Translate::init($use_lang);

		// Load version
		$version_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/version.ini', 'version');
		Zend_Registry::set("gd.version", $version_conf->gd->version);
	}

	protected function _initCheckSetup()
	{
		// Pass config to the VerifySetup controller to check our setup environment and  we're all OK to proceed
		$this->bootstrap('frontController');
		$frontController = $this->getResource('frontcontroller');
		$frontController->registerPlugin(new GD_Plugin_VerifySetup());
	}

	protected function _initNavigation()
	{
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();

		$auth = Zend_Auth::getInstance();
		$logged_in = ($auth->hasIdentity());
		$view->logged_in = $logged_in;

		$frontController = $this->getResource('frontcontroller');
		$frontController->registerPlugin(new GD_Plugin_Navigation($view));
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
				'project' => '[a-z0-9-]+',
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
				'project' => '[a-z0-9-]+',
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
		$view->headScript()->appendFile("/js/generate_slug.js");
		$view->headScript()->appendFile("/js/form_processing.js");

		// Base page title
		//$view->headTitle('GoDeploy');
		$view->headTitle('GoDeploy')
			->setSeparator(' - ')
			->setDefaultAttachOrder(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
	}
}

