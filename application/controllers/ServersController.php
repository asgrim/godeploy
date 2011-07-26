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
 * @author James Titcumb, Simon Wade
 * @link http://www.godeploy.com/
 */
class ServersController extends Zend_Controller_Action
{
	private $_method;

	public function init()
	{
		if($this->_request->getActionName() != "index")
		{
			if($this->_request->getActionName() == "add")
			{
				$this->_method = "add";
			}
			else if($this->_request->getActionName() == "edit")
			{
				$this->_method = "edit";
			}

			$this->_forward("index");
		}
	}

	public function indexAction()
	{
    	$form = new GDApp_Form_ServerSettings();
    	$this->view->form = $form;

		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();

		$server_id = $this->_request->getParam('id', 0);

		if($server_id > 0)
		{
			$servers->find($server_id, $server);
		}
		else
		{
			// new server...
		}
		$this->view->server = $server;

		if($this->getRequest()->isPost())
		{
			// do post stuff
		}
		else
		{
			$data = array(
				'name' => $server->getName(),
				'hostname' => $server->getHostname(),
				'connectionTypeId' => $server->getConnectionTypesId(),
			);

    		$form->populate($data);
		}
	}
}