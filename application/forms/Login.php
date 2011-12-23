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
class GDApp_Form_Login extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('login_form')
			->setAction('/auth/login')
			->setMethod('post');

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel(_r('Username'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter your User Name'));
		$username->addValidators(array($not_empty));


		$password = new Zend_Form_Element_Password('password');
		$password->setLabel(_r('Password'))
			->setRequired(true)
			->addFilter('StripTags');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter your Password'));
		$password->addValidators(array($not_empty));


		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/login.png');

		$this->addElements(
			array(
				$username,
				$password,
				$submit,
			)
		);
	}
}
