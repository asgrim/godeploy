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

		$router->addRoute('default', $defaultRoute);
		$router->addRoute('project', $projectRoute);
	}
}

