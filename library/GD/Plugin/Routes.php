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
 * Set up the routes for nice URLs
 *
 * @author james
 */
class GD_Plugin_Routes extends Zend_Controller_Plugin_Abstract
{

	/**
	 * Before routes are started, add our routes in to be processed by Router
	 *
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::routeStartup()
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();

		// Standard router
		$defaultRoute = new Zend_Controller_Router_Route(
			':controller/:action',
			array(
				'module'=>'default',
				'controller'=>'index',
				'action'=>'index',
			)
		);

		// Project specific route e.g. /project/my-project/servers/edit/1
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
}