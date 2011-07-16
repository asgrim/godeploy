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
class AuthController extends Zend_Controller_Action
{
    public function loginAction()
    {
    	if($this->_request->isPost())
		{
			$credentials = array();
			$credentials['username'] = $this->_request->getPost('username');
			$credentials['password'] = $this->_request->getPost('password');

			$adapter = $this->getAuthAdapter($credentials);

			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($adapter);

			if($result->isValid())
			{
				$this->_redirect("/home");
			}
			else
			{
				$this->executeLogout();
				$this->_redirect("/");
			}
		}
		else
		{
			$this->_redirect("/");
		}
    }

    private function executeLogout()
    {
    	$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
    }

    public function logoutAction()
    {
    	$this->executeLogout();
		$this->_redirect("/");
    }

    public function getAuthAdapter(array $credentials)
    {
    	return new GD_Auth_Database($credentials['username'], $credentials['password']);
    }
}
