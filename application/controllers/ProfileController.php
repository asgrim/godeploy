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
 * @author James Titcumb, Jon Wigham, Simon Wade
 * @link http://www.godeploy.com/
 */
class ProfileController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$this->_redirect('/profile/changepassword/');
    }

	public function changepasswordAction()
	{
		$form = new GDApp_Form_ChangePassword();
		if($this->getRequest()->isPost())
		{
			if($form->isValid($this->getRequest()->getParams()))
			{
				$password = $this->_request->getParam('password');
				$username = Zend_Auth::getInstance()->getIdentity();
				$crypt = new GD_Crypt();
				$userMapper = new GD_Model_UsersMapper();
				$user = $userMapper->getUserByName($username);
				$user->setPassword($crypt->makeHash($password));
				$userMapper->save($user);
				$this->view->success = true;
			}
		}
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/profile.css");
		$this->view->form = $form;
	}
}
