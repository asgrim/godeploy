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
class GD_Validate_GitUrl extends Zend_Validate_Abstract
{
	const HTTP_NOT_SUPPORTED = 'http_not_supported';
	const MALFORMATTED = 'malformatted';

	protected $_messageTemplates = array(
		self::HTTP_NOT_SUPPORTED => "HTTP/HTTPS Git repositories are not supported",
		self::MALFORMATTED => "The URL did not look like a valid Git URL",
	);

	public function isValid($value)
	{
		$this->_setValue($value);

		if(substr($value, 0, 6) == "git://")
		{
			return true; // git://anything
		}
		else if(substr($value, 0, 8) == "https://" || substr($value, 0, 7) == "http://")
		{
			$this->_error(self::HTTP_NOT_SUPPORTED);
			return false;
		}
		else if(preg_match("/^[a-zA-Z_.-]+@[a-zA-Z0-9.-]+:[a-zA-Z0-9\/_.-]+(.git)?$/", $value))
		{
			return true; // git@github.com:repo.git
		}

		$this->_error(self::MALFORMATTED);
		return false;
	}
}