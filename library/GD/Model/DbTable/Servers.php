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
class GD_Model_DbTable_Servers extends Zend_Db_Table_Abstract
{
	protected $_name = "servers";

	protected $_referenceMap = array(
		'GD_Model_DbTable_ConnectionTypes' => array(
			'columns' => 'connection_types_id',  // the column in the 'servers' table which is used for the join
			'refTableClass' => 'GD_Model_DbTable_ConnectionTypes',  // the public_keys table name
			'refColumns' => 'id' // the primary key of the foreign table
		)
	);

	protected $_dependentTables = array('GD_Model_DbTable_Deployments');
}