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
class GDApp_Form_ServerSettings extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('serverSettings');

		$server_name = new Zend_Form_Element_Text('name');
		$server_name->setLabel('Name')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$hostname = new Zend_Form_Element_Text('hostname');
		$hostname->setLabel('Hostname')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$ct_map = new GD_Model_ConnectionTypesMapper();
		$connection_types = $ct_map->fetchAll();

		$connection_type_id = new Zend_Form_Element_Select('connectionTypeId');
		$connection_type_id->setLabel('Connection Type')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		foreach($connection_types as $connection_type)
		{
			$connection_type_id->addMultiOption($connection_type->getId(), $connection_type->getName());
		}

		$port = new Zend_Form_Element_Text('port');
		$port->setLabel('Port')
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$username = new Zend_Form_Element_Text('username');
		$username->setLabel('Username')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('autocomplete', 'off');

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('autocomplete', 'off')
			->setAttrib('renderPassword', true);

		$report_path = new Zend_Form_Element_Text('remotePath');
		$report_path->setLabel('Remote Path')
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim');

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save Changes');

		$this->addElements(array(
			$server_name,
			$hostname,
			$connection_type_id,
			$port,
			$username,
			$password,
			$report_path,
			$submit,
		));
	}
}
?>