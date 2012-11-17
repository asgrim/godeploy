<?php

class GD_Controller_Action_Helper_GetProjectFromUrl extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		$projects = new GD_Model_ProjectsMapper();

		$project_slug = $this->getRequest()->getParam("project");

		if ($project_slug != "")
		{
			$project = $projects->getProjectBySlug($project_slug);
		}

		if (!isset($project) || is_null($project))
		{
			throw new GD_Exception("Project '{$project_slug}' was not set up.");
		}

		return $project;
	}
}