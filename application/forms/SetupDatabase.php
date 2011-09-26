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
class GDApp_Form_SetupDatabase extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('databasesetup_form');

		$hostname = new Zend_Form_Element_Text('hostname');
		$hostname->setLabel(_r('Hostname'))
			->setRequired(true)
			->addFilter('StripTags')
			->addValidator('NotEmpty');

		$username = new Zend_Form_Element_Text('db_username');
		$username->setLabel(_r('Username'))
			->setRequired(true)
			->addFilter('StripTags')
			->addValidator('NotEmpty');

		$password = new Zend_Form_Element_Password('db_password');
		$password->setLabel(_r('Password'))
			->setRequired(true)
			->addFilter('StripTags')
			->addValidator('NotEmpty');

		$dbname = new Zend_Form_Element_Text('dbname');
		$dbname->setLabel(_r('Database Name'))
			->setRequired(true)
			->addFilter('StripTags')
			->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/next.png')
			->setAttrib('style', 'float: right;');

		$this->addElements(array(
			$hostname,
			$username,
			$password,
			$dbname,
			$submit,
		));
	}
}