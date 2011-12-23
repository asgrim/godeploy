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
class GDApp_Form_ServerSettings extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('serverSettings');

		$server_name = new Zend_Form_Element_Text('name');
		$server_name->setLabel(_r('Name'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Server Name'));
		$server_name->addValidators(array($not_empty));

		$hostname = new Zend_Form_Element_Text('hostname');
		$hostname->setLabel(_r('Hostname'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Hostname'));
		$hostname->addValidators(array($not_empty));

		$ct_map = new GD_Model_ConnectionTypesMapper();
		$connection_types = $ct_map->fetchAll();

		$connection_type_id = new Zend_Form_Element_Select('connectionTypeId');
		$connection_type_id->setLabel(_r('Connection Type'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please choose a Connection Type'));
		$connection_type_id->addValidators(array($not_empty));

		foreach ($connection_types as $connection_type)
		{
			$connection_type_id->addMultiOption($connection_type->getId(), $connection_type->getName());
		}

		$port = new Zend_Form_Element_Text('port');
		$port->setLabel(_r('Port'))
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Port Number'));
		$port->addValidators(array($not_empty));

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel(_r('Username'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('autocomplete', 'off');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Username'));
		$username->addValidators(array($not_empty));

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->setAttrib('autocomplete', 'off')
			->setAttrib('renderPassword', true);
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Password'));
		$password->addValidators(array($not_empty));

		$report_path = new Zend_Form_Element_Text('remotePath');
		$report_path->setLabel(_r('Remote Path'))
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim');


		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/save-changes.png');

		$this->addElements(
			array(
				$server_name,
				$hostname,
				$connection_type_id,
				$port,
				$username,
				$password,
				$report_path,
				$submit,
			)
		);
	}
}
