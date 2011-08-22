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
class HistoryController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$projects = new GD_Model_ProjectsMapper();
		$project_slug = $this->_getParam("project");
		if($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

        $deployments_map = new GD_Model_DeploymentsMapper();
        $this->view->deployments = $deployments_map->getDeploymentsByProject($project->getId());

        $this->view->project = $project;

        $this->view->headLink()->appendStylesheet("/css/pages/history.css");
    }
}

