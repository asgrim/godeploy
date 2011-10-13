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
 * @author James Titcumb, Simon Wade, Jon Wigham
 * @link http://www.godeploy.com/
 */
class GD_Validate_GitBranch extends Zend_Validate_Abstract
{
	const INVALID = 'invalid';
	const NO_REPO = 'no_repo';

	protected $_messageTemplates = array(
		self::INVALID => "This branch does not exist",
		self::NO_REPO => "Repository did not exist or was not a valid repository",
	);

	private $_project;

	public function __construct(GD_Model_Project $project)
	{
		$this->_project = $project;
	}

	public function isValid($value)
	{
		$this->_setValue($value);

		$git = new GD_Git($this->_project);

		try
		{
			if($git->checkValidRepository())
			{
				if($git->gitCheckout($value))
				{
					return true;
				}
			}

			$this->_error(self::INVALID);
			return false;
		}
		catch(GD_Exception $ex)
		{
			$this->_error(self::NO_REPO);
			return false;
		}

		/*if(@chdir($this->_gitdir))
		{
			$sh = new GD_Shell();
			$sh->Exec('git checkout ' . preg_replace("/[^-\/a-zA-Z0-9_-]/", "", $value));

			if($sh->getLastError() == 0)
			{
				return true;
			}
			else
			{
				$this->_error(self::INVALID);
				return false;
			}
		}
		else
		{
			$this->_error(self::NO_REPO);
			return false;
		}*/
	}
}