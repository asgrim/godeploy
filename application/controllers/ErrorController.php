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
class ErrorController extends Zend_Controller_Action
{
	public function privilegeAction()
	{
		$this->_helper->viewRenderer('error');
		$this->getResponse()->setHttpResponseCode(404);

		$ext_inf = '<p style="text-align: center; font-size: 172px; font-weight: bold; color: #348e1c;">This = 404</p>';

		$this->view->message = _r("Could not find the page you were looking for...");
		$this->view->extended_information = $ext_inf;

		// Log exception
		GD_Debug::Log("ACL access denied for URL '{$_SERVER['REQUEST_URI']}'", GD_Debug::DEBUG_BASIC);
	}

	public function databaseAction()
	{
		$this->_helper->viewRenderer('error');
		$this->getResponse()->setHttpResponseCode(500);

		$ext_inf = '<p>' . _r("The most likely cause is the settings in the configuration are incorrect or the database server is unavailable. One of the following solutions may help to fix this problem:") . '</p>';
		$ext_inf .= '<ol>';
		$ext_inf .= '<li>' . _r("Ensure the database is running") . '</li>';
		$ext_inf .= '<li>' . _r("Check the settings in") . ' <span style="font-family: monospace;">' . realpath(APPLICATION_PATH . "/configs/config.ini") . '</span> ' . _r("are correct") . '</li>';
		$ext_inf .= '</ol>';

		$this->view->message = _r("Error establishing database connection");
		$this->view->extended_information = $ext_inf;

		// Log exception
		GD_Debug::Log("Database connection failed...", GD_Debug::DEBUG_BASIC);
	}

	public function recloneAction()
	{
		$this->_helper->viewRenderer('error');
		$this->getResponse()->setHttpResponseCode(500);

		$proj = $this->_getParam('project');
		$return = $this->_getParam('return');

		$ext_inf = "<p>The repository cache for '{$proj}' has gone wrong somehow and needs re-cloning.</p>";
		$ext_inf .= "<p>To reclone the repository, <strong><a href=\"/project/{$proj}/settings/reclone?return={$return}\">click this link now</a></strong> and this will hopefully fix the problem.";

		$this->view->message = _r("Local repository cache is out of sync.");
		$this->view->extended_information = $ext_inf;

		// Log exception
		GD_Debug::Log("Reclone required for {$proj} (reclone error)", GD_Debug::DEBUG_BASIC);
	}

	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');

		if (!$errors) {
			$this->view->message = 'You have reached the error page';
			return;
		}

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = _r("Could not find the page you were looking for...");
				break;
			default:
				// application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->message = 'Application error';

				if($errors->exception instanceof GD_Exception)
				{
					$this->view->extended_information = $errors->exception->getMessage();
				}
				break;
		}

		// Log exception
		GD_Debug::Log("Exception: " . $this->view->message, GD_Debug::DEBUG_BASIC);

		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->view->exception = $errors->exception;
		}

		$this->view->request   = $errors->request;
	}
}

