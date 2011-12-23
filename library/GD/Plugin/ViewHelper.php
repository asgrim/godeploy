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

/**
 * Initialise doctypes, global css+js, etc.
 *
 * @author james
 */
class GD_Plugin_ViewHelper extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Initialise doctypes, global css+js, etc.
	 *
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;

		// Set doctype to HTML5
		$view->doctype('HTML5');

		// Meta tags
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');

		// Add default css files
		$view->headLink()->appendStylesheet("/css/template/common.css");
		$view->headLink()->appendStylesheet("/css/template/main.css");
		$view->headLink()->appendStylesheet("/css/template/header.css");
		$view->headLink()->appendStylesheet("/css/template/footer.css");
		$view->headLink()->appendStylesheet("/css/template/wrappers.css");

		// Add any standard javascript files/libraries
		$view->headScript()->appendFile("/js/prototype/1.7.js");
		$view->headScript()->appendFile("/js/scriptaculous/1.9.0.js");
		$view->headScript()->appendFile("/js/common.js");
		$view->headScript()->appendFile("/js/generate_slug.js");
		$view->headScript()->appendFile("/js/form_processing.js");

		// Base page title
		$view->headTitle('GoDeploy')
			->setSeparator(' - ')
			->setDefaultAttachOrder(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
	}
}