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
 * Adapter interface - specifies a contract that can be used to make a deployment
 *
 * @author james
 */
interface GD_Deployment_AdapterInterface
{
	/**
	 * Implement any connection requirements
	 */
	public function connect();

	/**
	 * Implement any disconnection requirements
	 */
	public function disconnect();

	/**
	 * Upload a file from the remote server
	 *
	 * @param string $local_file
	 * @param string $remote_file
	 */
	public function upload($local_file, $remote_file);

	/**
	 * Delete a file from the remote server
	 *
	 * @param string $remote_file
	 */
	public function delete($remote_file);

	/**
	 * Test the connection works
	 *
	 * @return boolean
	 */
	public function testConnection();

	/**
	 * Get the last error message
	 *
	 * @return string
	 */
	public function getLastError();
}