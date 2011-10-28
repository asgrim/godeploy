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
class HistoryController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function populateView()
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
	}

	public function indexAction()
	{
		$this->populateView();

		$this->_helper->viewRenderer('index');
		$this->view->headTitle('History');

		$this->view->headLink()->appendStylesheet("/css/template/table.css");
		$this->view->headLink()->appendStylesheet("/css/pages/history.css");
	}

	public function jsonAction()
	{
		$this->populateView();

		// Disable Zend view rendering and set content type
		$this->_response->setHeader('Content-type', 'text/plain');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		$output = array();

		if(is_array($this->view->deployments) && count($this->view->deployments) > 0)
		{
			foreach($this->view->deployments as $deployment)
			{
				$item = array();
				$item["id"] = $deployment->getId();
				$item["date"] = $deployment->getWhen("d/m/Y H:i:s");
				$item["server"] = $deployment->getServer()->getDisplayName();
				$item["from_rev"] = $deployment->getFromRevision();
				$item["to_rev"] = $deployment->getToRevision();
				$item["comment"] = $deployment->getComment();
				$item["status"] = $deployment->getDeploymentStatus()->getShortName();
				$output[$deployment->getId()] = $item;
			}
		}

		echo json_encode($output);
	}

	public function csvAction()
	{
		$this->populateView();

		// Disable Zend layout rendering and set content type
		$this->_response->setHeader('Content-type', 'text/plain');
		$this->_helper->layout->disableLayout();

		$this->_helper->viewRenderer('csv');
	}

	public function rssAction()
	{
		$this->populateView();

		// Basic information
		$url = $this->_request->getScheme() . "://" . $this->getRequest()->getHttpHost() . $this->getRequest()->getRequestUri();
		$author = array(
			'name'  => 'GoDeploy RSS Generator',
			'email' => 'info@godeploy.com',
			'uri'   => $url,
		);

		// Disable Zend view rendering and set content type
		$this->_response->setHeader('Content-type', 'text/xml');
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();

		// RSS/Atom header
		$feed = new Zend_Feed_Writer_Feed();
		$feed->setTitle('GoDeploy deployment history');
		$feed->setDescription('GoDeploy deployment history');
		$feed->setLink($url);
		$feed->setFeedLink($url, 'rss');
		$feed->addAuthor($author);
		$feed->setDateModified(time());

		if(is_array($this->view->deployments) && count($this->view->deployments) > 0)
		{
			foreach($this->view->deployments as $deployment)
			{
				$content = "Deployed on: " . $deployment->getWhen("d/m/Y H:i:s") . "<br />";
				$content .= "To Server: " . $deployment->getServer()->getDisplayName() . "<br />";
				$content .= "From: " .  substr($deployment->getFromRevision(), 0, 7) . "<br />";
				$content .= "To: " .  substr($deployment->getToRevision(), 0, 7) . "<br />";
				$content .= "Comment: " . $deployment->getComment() . "<br />";
				$content .= "Status: " . $deployment->getDeploymentStatus()->getShortName() . "<br />";

				$entry = $feed->createEntry();
				$entry->setTitle("Deployment " . $deployment->getWhen("d/m/Y H:i:s"));
				$entry->setLink(str_replace("/history/rss", "/deploy/result/" . $deployment->getId(), $url));
				$entry->addAuthor($author);
				$entry->setDateModified(time());
				$entry->setDateCreated(new Zend_Date($deployment->getWhen("Y-m-d H:i:s"), Zend_Date::ISO_8601));
				$entry->setDescription('GoDeploy deployment ' . $deployment->getId());
				$entry->setContent($content);
				$feed->addEntry($entry);
			}
		}

		echo $feed->export('rss');
	}
}

