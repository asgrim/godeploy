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
class AdminController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->view->headTitle('Administration');
		$this->view->headLink()->appendStylesheet("/css/template/table.css");

		$settings = GD_Config::getAll();
		$this->view->settings = $settings;

		$ssh_keys = new GD_Model_SSHKeysMapper();
		$ssh_key = new GD_Model_SSHKey();
		$ssh_keys->find($settings['ssh_key_id'], $ssh_key);
		$this->view->sshKey = $ssh_key;
	}

	public function settingsAction()
	{
		$this->view->headTitle('Administration')->prepend('User Management');
		$this->view->headLink()->appendStylesheet("/css/template/form.css");

		$form = new GDApp_Form_AppSettings();

		$this->view->form = $form;

		if ($this->getRequest()->isPost())
		{
			if ($this->_getParam('enable_url_trigger') == '1')
			{
				$not_empty = new Zend_Validate_NotEmpty();
				$not_empty->setMessage(_r('If you enable the URL trigger, you must set a token for the trigger.'));
				$form->url_trigger_token->addValidators(array($not_empty));
				$form->url_trigger_token->setRequired(true);
			}

			if ($form->isValid($this->getRequest()->getParams()))
			{
				$save_fields = array_keys($form->getElements());

				foreach($save_fields as $field)
				{
					if ($field == 'btn_submit')
					{
						continue;
					}
					GD_Config::set($field, $this->_getParam($field));
				}

				$this->_redirect('/admin');
			}
		}
		else
		{
			$settings = GD_Config::getAll();

			$form->populate($settings);
		}
	}

	public function usersAction()
	{
		$this->view->headTitle('Administration')->prepend('User Management');
		$this->view->headLink()->appendStylesheet("/css/template/table.css");

		$userMapper = new GD_Model_UsersMapper();
		$users = $userMapper->fetchAll(null, 'name');

		$this->view->users = $users;
	}

	public function userAction()
	{
		$this->view->headLink()->appendStylesheet("/css/template/form.css");
		$this->view->headLink()->appendStylesheet("/css/pages/project_servers.css");

		$users = new GD_Model_UsersMapper();
		$user = new GD_Model_User();

		$form_options = array();

		if($this->_getParam('id') > 0)
		{
			$this->view->headTitle('Edit User');
			$users->find($this->_getParam('id'), $user);
			$form_options['current_user'] = $user->getName();
			$form = new GDApp_Form_User($form_options);
		}
		else
		{
			$this->view->headTitle('Add User');
			$form = new GDApp_Form_User();
			$form->password->setRequired(true)->setDescription('');
			$user->setDateAdded(date("Y-m-d H:i:s"));
		}

		$this->view->form = $form;

		if($this->getRequest()->isPost())
		{
			if ($form->isValid($this->getRequest()->getParams()))
			{
				if($this->_getParam('password', false))
				{
					$crypt = new GD_Crypt();
					$user->setPassword($crypt->makeHash($this->_getParam('password')));
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

				$this->_redirect('/admin/users');
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
