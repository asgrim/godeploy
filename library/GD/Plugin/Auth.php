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
 * Authentication ACL plugin to ensure the current identity has access to the
 * resource they requested
 *
 * @author james
 */
class GD_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var Zend_Auth
	 */
	private $_auth;

	/**
	 * @var Zend_Acl
	 */
	private $_acl;

	/**
	 * @var array Page to redirect to if no identity found (not logged in)
	 */
	private $_noauth = array('module' => 'default',
							'controller' => 'index',
							'action' => 'index');

	/**
	 * @var array Page to redirect to if access to resource is denied
	 */
	private $_noacl = array('module' => 'default',
							'controller' => 'error',
							'action' => 'privilege');

	/**
	 * Create the GD_Plugin_Auth controller plugin
	 */
	public function __construct()
	{
		$this->_auth = Zend_Auth::getInstance();
		$this->_acl = new GD_Acl($this->_auth);
	}

	/**
	 * Check that the user has an identity (is logged in) and that they have
	 * sufficient access to the resource (page) requested.
	 *
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// If we are on the error controller, return immediately to prevent
		// any database errors happening on error page
		if($request->controller == "error") return;

		// First determine what role we have (admin, member or guest)
		if ($this->_auth->hasIdentity())
		{
			$username = Zend_Auth::getInstance()->getIdentity();
			$userMapper = new GD_Model_UsersMapper();
			$user = $userMapper->getUserByName($username);

			if($user->isAdmin())
			{
				$role = 'admin';
			}
			else
			{
				$role = 'member';
			}
		}
		else
		{
			$role = 'guest';
		}

		// Set the initial request - these will be unmodified if access allowed
		$controller = $request->controller;
		$action = $request->action;
		$module = $request->module;
		$resource = $controller;

		if (!$this->_acl->has($resource)) {
			$resource = null;
		}

		// Use Zend_Acl to check access permissions
		if (!$this->_acl->isAllowed($role, $resource, $action))
		{
			if (!$this->_auth->hasIdentity())
			{
				$module = $this->_noauth['module'];
				$controller = $this->_noauth['controller'];
				$action = $this->_noauth['action'];
			}
			else
			{
				$module = $this->_noacl['module'];
				$controller = $this->_noacl['controller'];
				$action = $this->_noacl['action'];
			}
		}

		// If the module/controller/action has changed, change the request
		if($request->controller != $controller
			|| $request->action != $action
			|| $request->module != $module)
		{
			$request->setModuleName($module);
			$request->setControllerName($controller);
			$request->setActionName($action);
		}
	}

}