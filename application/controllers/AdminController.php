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
class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$this->view->headLink()->appendStylesheet("/css/template/table.css");

		$userMapper = new GD_Model_UsersMapper();
		$users = $userMapper->fetchAll();

		$this->view->users = $users;
    }

    public function userAction()
    {
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/project_servers.css");

    	$form = new GDApp_Form_User();
    	$this->view->form = $form;

    	$users = new GD_Model_UsersMapper();
    	$user = new GD_Model_User();

    	if($this->_getParam('id') > 0)
    	{
    		$users->find($this->_getParam('id'), $user);
    	}
    	else
    	{
    		$form->password->setRequired(true)->setDescription('');
    	}

    	if($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getParams()))
			{
				if($this->_getParam('password', false))
				{
					$crypt = new GD_Crypt();
					$user->setPassword($crypt->makeHash($password));
				}

				$user->setName($this->_getParam('username'));

				if($this->_getParam('active'))
				{
					$user->enableUser();
				}
				else
				{
					$user->disableUser();
				}

				$user->setAdmin($this->_getParam('admin'));

				$users->save($user);

				$this->_redirect('/admin');
			}
		}
		else
		{
			$data = array(
				'username' => $user->getName(),
				'admin' => $user->isAdmin(),
				'active' => $user->isActive(),
			);

    		$form->populate($data);
		}
    }
}
