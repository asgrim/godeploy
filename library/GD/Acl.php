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

/**
 * Allow/deny things using Zend_Acl and GD_Plugin_Auth
 * @author james
 *
 */
class GD_Acl extends Zend_Acl
{
	public function __construct(Zend_Auth $auth)
	{
		#parent::__construct();

		// Add resources
		$this->add(new Zend_Acl_Resource('index'));
		$this->add(new Zend_Acl_Resource('error'));
		$this->add(new Zend_Acl_Resource('auth'));
		$this->add(new Zend_Acl_Resource('profile'));
		$this->add(new Zend_Acl_Resource('home'));
		$this->add(new Zend_Acl_Resource('history'));
		$this->add(new Zend_Acl_Resource('settings'));
		$this->add(new Zend_Acl_Resource('servers'));
		$this->add(new Zend_Acl_Resource('deploy'));

		// Add roles
		$this->addRole(new Zend_Acl_Role('guest'));
		$this->addRole(new Zend_Acl_Role('member'), 'guest');
		$this->addRole(new Zend_Acl_Role('admin'), 'member');

		// Allow/deny roles to resources
		$this->allow('guest', 'index');
		$this->allow('guest', 'error');
		$this->allow('guest', 'auth');
		$this->allow('member', 'profile');
		$this->allow('member', 'home');
		$this->allow('member', 'history');
		$this->allow('member', 'settings');
		$this->allow('member', 'servers');
		$this->allow('member', 'deploy');
		$this->allow('admin');
	}
}