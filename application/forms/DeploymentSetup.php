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
class GDApp_Form_DeploymentSetup extends GD_Form_Abstract
{
	public function __construct($project_id, $options = null)
	{
		parent::__construct($options);

		$this->setName('deploymentSetup');

		$servers_map = new GD_Model_ServersMapper();
		$servers = $servers_map->getServersByProject($project_id);

		if(!is_array($servers) || count($servers) == 0)
		{
			throw new GD_Exception("There are no servers configured for this project.");
		}

		$server_id = new Zend_Form_Element_Select('serverId');
		$server_id->setLabel(_r('Server'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty');

		foreach($servers as $server)
		{
			$server_id->addMultiOption($server->getId(), $server->getDisplayName());
		}

		$from_revision = new Zend_Form_Element_Text('fromRevision');
		$from_revision->setLabel(_r('Current revision'))
			->setRequired(false)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setAttrib('readonly', 'readonly')
			->setAttrib('disabled', 'disabled');

		$to_revision = new Zend_Form_Element_Text('toRevision');
		$to_revision->setLabel(_r('Deploy revision or tag'))
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->setDescription('<a href="#" onclick="getLatestRevision();">Click to get latest revision</a><span id="get_latest_revision_status"></span>');

		$submitRun = new Zend_Form_Element_Image('submitRun');
		$submitRun->setImage('/images/buttons/small/deploy.png');

		$submitPreview = new Zend_Form_Element_Image('submitPreview');
		$submitPreview->setImage('/images/buttons/small/inverted/preview.png');
		$submitPreview->class = "preview";

		$this->addElements(array(
			$server_id,
			$from_revision,
			$to_revision,
			$submitRun,
			$submitPreview,
		));
	}
}
