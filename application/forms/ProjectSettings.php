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
class GDApp_Form_ProjectSettings extends GD_Form_Abstract
{
	public function __construct(GD_Model_Project $project, $options = null, $new_project = false)
	{
		parent::__construct($options);

		$this->setName('projectSettings');

		$project_name = new Zend_Form_Element_Text('name');
		$project_name->setLabel(_r('Project Name'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the project name'));
		$project_name->addValidators(array($not_empty));

		// if we're adding a new project, we need to make sure it's unique
		if ($new_project)
		{
			$unique_name = new GD_Validate_UniqueName();
			$project_name->addValidators(array($unique_name));
		}

		$git_validator = new GD_Validate_GitUrl();

		$repository_url = new Zend_Form_Element_Text('repositoryUrl');
		$repository_url->setLabel(_r('Repository URL'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Repository URL'));
		$repository_url->addValidators(array($not_empty, $git_validator));

		$deployment_branch = new Zend_Form_Element_Text('deploymentBranch');
		$deployment_branch->setLabel(_r('Deployment Branch'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the name of the Deployment Branch'));
		$deployment_branch->addValidators(array($not_empty));

		if(!$new_project)
		{
			$deployment_branch->addValidator(new GD_Validate_GitBranch($project));
		}

		$public_key = new Zend_Form_Element_Textarea('publicKey');
		$public_key->setLabel(_r('Public Key'))
			->setRequired(false)
			->setAttrib('readonly', 'readonly');
		$not_empty = new Zend_Validate_NotEmpty();
		$not_empty->setMessage(_r('Please enter the Public Key'));
		$public_key->addValidators(array($not_empty));


		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/save-changes.png');
		$submit->class = "processing_btn size_small";

		$this->addElements(array(
			$project_name,
			$repository_url,
			$deployment_branch,
			$public_key,
			$submit,
		));
	}
}
