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
class GDApp_Form_User extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel(_r('Username'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->addValidator(new GD_Validate_UniqueUser($options['current_user']));

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel(_r('Password'))
			->setAttrib('autocomplete', 'off')
			->setDescription('To leave the user\'s password unchanged, leave this empty.');

		$passwordConfirm = new Zend_Form_Element_Password('passwordconf');
		$passwordConfirm->setLabel(_r('Confirm Password'))
			->addValidator('Identical', false, array('token' => 'password'))
			->setAttrib('autocomplete', 'off');

		$admin = new Zend_Form_Element_Checkbox('admin');
		$admin->setLabel(_r('Is this user an admin?'));

		$active = new Zend_Form_Element_Checkbox('active');
		$active->setLabel(_r('Is this user active?'));

		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/login.png');

		$this->addElements(array(
			$username,
			$password,
			$passwordConfirm,
			$admin,
			$active,
			$submit,
		));
	}
}
