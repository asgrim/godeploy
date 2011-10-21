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
class GD_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
	private $_view;

	public function __construct($view)
	{
		$this->_view = $view;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if($request->controller == "error") return;

		$nav = array();

		if ($this->_view->logged_in)
		{
			$nav[] = array(
				"label" => "Home",
				"id" => "home-link",
				"uri" => "/home"
			);

			// If we're in a project, add in the things you can do
			if ($project_slug = $request->getParam("project"))
			{
				$projects = new GD_Model_ProjectsMapper();
				$project = $projects->getProjectBySlug($project_slug);

				if ($project instanceof GD_Model_Project)
				{
					$nav[] = array(
						"label" => "History",
						"id" => "deployments-link",
						"uri" => "/project/{$project_slug}/history"
					);

					$nav[] = array(
						"label" => "Settings",
						"id" => "settings-link",
						"uri" => "/project/{$project_slug}/settings"
					);

					$nav[] = array(
						"label" => "Deploy",
						"id" => "deploy-link",
						"uri" => "/project/{$project_slug}/deploy"
					);
				}
			}
			else
			{
				$nav[] = array(
					"label" => "Profile",
					"id" => "profile-link",
					"uri" => "/profile"
				);

				$username = Zend_Auth::getInstance()->getIdentity();
				$userMapper = new GD_Model_UsersMapper();
				$user = $userMapper->getUserByName($username);

				if($user->isAdmin())
				{
					$nav[] = array(
						"label" => "Admin",
						"id" => "admin-link",
						"uri" => "/admin"
					);
				}
			}
		}
		else
		{
			$nav[] = array(
				"label" => "Login",
				"id" => "login-link",
				"uri" => "/auth/login"
			);
		}

		$nav = new Zend_Navigation($nav);
		$this->_view->navigation($nav);

		$uri = $request->getRequestUri();
		$page = $this->_view->navigation()->findOneBy("uri", $uri);
		if ($page) $page->setActive();
	}
}