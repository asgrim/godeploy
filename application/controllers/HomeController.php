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
class HomeController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$mprojects = new GD_Model_ProjectsMapper();

		$projects = $mprojects->fetchAll();

		$this->view->projects = $projects;

		$this->view->headTitle('Home');
		$this->view->headLink()->appendStylesheet("/css/pages/home.css");
		$this->view->headLink()->appendStylesheet("/css/template/table.css");
	}
}
