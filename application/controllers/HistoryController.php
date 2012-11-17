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

	public function populateView($page = 0)
	{
		$project = $this->_helper->getProjectFromUrl();

		// Load the mapper class
		$deployments_map = new GD_Model_DeploymentsMapper();

		$items_per_page = GD_Config::get("rows_per_history_page");

		// Pagination is enabled if greater than zero
		if($page > 0)
		{
			$total_deployments = $deployments_map->getNumDeployments($project->getId());
			$last_page = ceil($total_deployments/$items_per_page);

			if($page > $last_page)
			{
				$page = $last_page;
			}

			$limit = $items_per_page;
			$offset = ($page - 1) * $items_per_page;

			$this->view->current_page = $page;
			$this->view->last_page = $last_page;
			$this->view->total_deployments = $total_deployments;
		}
		else
		{
			$limit = $items_per_page;
			$offset = 0;
		}

		$this->view->deployments = $deployments_map->getDeploymentsByProject($project->getId(), $offset, $limit);

		$this->view->project = $project;
	}

	public function indexAction()
	{
		$page = (int)$this->_getParam("page", 1);

		$this->populateView($page);

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
				$item["author"] = $deployment->getUser()->getName();
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
				$content .= "Author: " . $deployment->getUser()->getName() . "<br />";
				$content .= "Status: " . $deployment->getDeploymentStatus()->getShortName() . "<br />";

				$entry = $feed->createEntry();
				$entry->setTitle("Deployment " . $deployment->getWhen("d/m/Y H:i:s"));
				$entry->setLink(str_replace("/history/rss", "/deploy/result/" . $deployment->getId(), $url));
				$entry->addAuthor($deployment->getUser()->getName(), $deployment->getUser()->getName() . '@' . $this->getRequest()->getHttpHost());
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

