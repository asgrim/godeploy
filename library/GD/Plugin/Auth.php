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
class GD_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	private $_auth;
	private $_acl;

	private $_noauth = array('module' => 'default',
							'controller' => 'index',
							'action' => 'index');

	private $_noacl = array('module' => 'default',
							'controller' => 'error',
							'action' => 'privilege');

	public function __construct($auth, $acl)
	{
		$this->_auth = $auth;
		$this->_acl = $acl;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_auth->hasIdentity())
		{
			$role = 'member';
		}
		else
		{
			$role = 'guest';
		}

		$controller = $request->controller;
		$action = $request->action;
		$module = $request->module;
		$resource = $controller;

		if (!$this->_acl->has($resource)) {
			$resource = null;
		}

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