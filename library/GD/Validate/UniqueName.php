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

/**
 * Validate that a project name is unique by checking the slug against database
 *
 * @author jon
 */
class GD_Validate_UniqueName extends Zend_Validate_Abstract
{
	const ISUNIQUE = 'isunique';

	protected $_messageTemplates = array(
		self::ISUNIQUE => "This name is not unique - please enter something different",
	);

	public function isValid($value)
	{
		$this->_setValue($value);

		$slug = MAL_Util_TextFormatting::MakeSlug($value);

		$m_projects = new GD_Model_ProjectsMapper();
		$existing_project = $m_projects->getProjectBySlug($slug);

		if (is_null($existing_project))
		{
			return true;
		}
		else
		{
			$this->_error(self::ISUNIQUE);
			return false;
		}
	}
}