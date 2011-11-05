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
class GD_Model_Config
{
	protected $_id;
	protected $_projects_id;
	protected $_filename;
	protected $_content;

	protected $_project;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setProjectsId($value)
	{
		$this->_projects_id = (int)$value;
		return $this;
	}

	public function getProjectsId()
	{
		return $this->_projects_id;
	}

	public function setFilename($value)
	{
		$this->_filename = (string)$value;
		return $this;
	}

	public function getFilename()
	{
		return $this->_filename;
	}

	public function setContent($value)
	{
		$this->_content = (string)$value;
		return $this;
	}

	public function getContent()
	{
		return $this->_content;
	}

	public function setProject(GD_Model_Project $obj)
	{
		$this->_project = $obj;
		return $this;
	}

	public function getProject()
	{
		return $this->_project;
	}
}
