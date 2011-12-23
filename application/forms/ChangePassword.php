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
class GDApp_Form_ChangePassword extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('changepassword_form');

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel(_r('New Password'))
			->setRequired(true)
			->addFilter('StripTags');
		$not_empty = new Zend_Validate_NotEmpty();
		$password->addValidators(array($not_empty));

		$passwordConfirm = new Zend_Form_Element_Password('passwordconf');
		$passwordConfirm->setLabel(_r('Confirm Password'))
			->setRequired(true)
			->addFilter('StripTags');
		$passwordConfirm->addValidators(array($not_empty));
		$passwordConfirm->addValidator('Identical', false, array('token' => 'password'));

		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/save-changes.png');

		$this->addElements(
			array(
				$password,
				$passwordConfirm,
				$submit
			)
		);
	}
}