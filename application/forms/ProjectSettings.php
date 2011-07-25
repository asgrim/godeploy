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
class GDApp_Form_ProjectSettings extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setName('projectSettings');

		$project_name = new Zend_Form_Element_Text('name');
		$project_name->setLabel('Project Name')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$repository_url = new Zend_Form_Element_Text('repositoryUrl');
		$repository_url->setLabel('Repository URL')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$deployment_branch = new Zend_Form_Element_Text('deploymentBranch');
		$deployment_branch->setLabel('Deployment Branch')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$public_key = new Zend_Form_Element_Textarea('publicKey');
		$public_key->setLabel('Public Key')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save Changes');

		$this->addElements(array(
			$project_name,
			$repository_url,
			$deployment_branch,
			$public_key,
			$submit,
		));
	}
}
?>