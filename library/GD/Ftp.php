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
 * FTP wrapper - handle uploading, deleting and FTP connections in OOP form
 *
 * @author james
 */
class GD_Ftp
{
	/**
	 * @var string Hostname of the server to connect to
	 */
	private $_hostname;

	/**
	 * @var string Username to use to connect to the server
	 */
	private $_username;

	/**
	 * @var string Password to use to connect to the server
	 */
	private $_password;

	/**
	 * @var int Port number (default 21)
	 */
	private $_port;

	/**
	 * @var string Default remote path - absolute or relative to start in when
	 *             connected
	 */
	private $_remote_path;

	/**
	 * @var resource Resource handle to the FTP server
	 */
	private $_handle;

	/**
	 * @var string Default present/current working directory
	 */
	private $_pwd;

	/**
	 * @var mixed Last error message
	 */
	private $_last_error;

	public function __construct(GD_Model_Server $server)
	{
		$this->_hostname = $server->getHostname();
		$this->_username = $server->getUsername();
		$this->_password = $server->getPassword();
		$this->_port = $server->getPort() ? $server->getPort() : 21;
		$this->_remote_path = $server->getRemotePath();
	}

	public function __destruct()
	{
		$this->disconnect();
	}

	public function getLastError()
	{
		return $this->_last_error;
	}

	/**
	 * Reset the pwd to the default pwd we set on connection
	 */
	private function resetPwd()
	{
		if(!$this->_handle)
		{
			throw new GD_Exception("Not connected (resetPwd).");
		}
		ftp_chdir($this->_handle, $this->_pwd);
	}

	/**
	 * Do a connection test - connect, upload a file, delete it and disconnect.
	 * Returns true on success, false on failure.
	 *
	 * If there was an error, we try to populate $this->_last_error which can
	 * be fetched externally with $ftp->getLastError();
	 *
	 * @return bool
	 */
	public function testConnection()
	{
		try
		{
			$this->connect();

			$test_content = "Some test content...";
			$test_file = tempnam(sys_get_temp_dir(), "gd_upload_test_");
			$remote_test_file = ".gd_test_file";
			file_put_contents($test_file, $test_content);
			chmod($test_file, 0777);

			$this->upload($test_file, $remote_test_file);
			$this->delete($remote_test_file);

			$this->disconnect();

			unlink($test_file);

			return true;
		}
		catch(GD_Exception $exception)
		{
			$this->_last_error = $exception->getMessage();
			return false;
		}
	}

	/**
	 * Forcefully try to change to a directory by creating the path if it
	 * doesn't exist on the remote server.
	 *
	 * @param string $dir Directory to change to on the remote server
	 * @throws GD_Exception
	 * @return bool True on success
	 */
	private function ftpChangeOrMakeDirectory($dir)
	{
		$folders = explode("/", $dir);

		for($i = 0; $i < count($folders); $i++)
		{
			if($folders[$i] == "")
			{
				if($i == 0 && count($folders) > 1)
				{
					if(!@ftp_chdir($this->_handle, "/"))
					{
						throw new GD_Exception("Failed to change to root directory using absolute path '$dir'");
					}
					continue;
				}
				else
				{
					return true;
				}
			}

			if(!@ftp_chdir($this->_handle, $folders[$i]))
			{
				$res = @ftp_mkdir($this->_handle, $folders[$i]);
				if($res === false)
				{
					throw new GD_Exception("Failed to create FTP directory {$folders[$i]}. pwd=" . ftp_pwd($this->_handle));
				}

				if(!@ftp_chdir($this->_handle, $folders[$i]))
				{
					throw new GD_Exception("Failed to change into new directory {$folders[$i]}. pwd=" . ftp_pwd($this->_handle));
				}
			}
		}
		return true;
	}

	/**
	 * Upload a file to the FTP server
	 *
	 * @param string $local_file File to upload
	 * @param string $remote_file File to upload to on remote server
	 * @throws GD_Exception
	 */
	public function upload($local_file, $remote_file)
	{
		if(!$this->_handle)
		{
			throw new GD_Exception("Not connected (upload).");
		}

		$remote_dir = str_replace(basename($remote_file), "", $remote_file);
		$this->ftpChangeOrMakeDirectory($remote_dir);

		if(!@ftp_put($this->_handle, basename($remote_file), $local_file, FTP_BINARY))
		{
			throw new GD_Exception("Failed to upload '{$local_file}' [pwd=" . ftp_pwd($this->_handle) . "]");
		}

		$this->resetPwd();
	}

	/**
	 * Delete a file from the FTP server
	 *
	 * @param string $remote_file File to delete
	 * @throws GD_Exception
	 */
	public function delete($remote_file)
	{
		if(!$this->_handle)
		{
			throw new GD_Exception("Not connected (delete).");
		}

		$remote_dir = str_replace(basename($remote_file), "", $remote_file);
		$this->ftpChangeOrMakeDirectory($remote_dir);

		if(!@ftp_delete($this->_handle, basename($remote_file)))
		{
			throw new GD_Exception("Failed to delete '{$remote_file}' [pwd=" . ftp_pwd($this->_handle) . "]");
		}

		$this->resetPwd();
	}

	/**
	 * Connect to the FTP server using PHP's builtin FTP functions
	 *
	 * @throws GD_Exception
	 */
	public function connect()
	{
		$this->_handle = @ftp_connect($this->_hostname, $this->_port, 10);

		if($this->_handle == false)
		{
			throw new GD_Exception("Couldn't connect to FTP server on '{$this->_hostname}:{$this->_port}'");
		}

		if(!@ftp_login($this->_handle, $this->_username, $this->_password))
		{
			throw new GD_Exception("Failed to log in to '{$this->_hostname}:{$this->_port}' with user '{$this->_username}'");
		}

		$this->ftpChangeOrMakeDirectory($this->_remote_path);

		$this->_pwd = ftp_pwd($this->_handle);
	}

	/**
	 * Disconnect from the currently connected FTP server, if currently
	 * connected.
	 */
	public function disconnect()
	{
		if($this->_handle)
		{
			ftp_close($this->_handle);
		}
	}
}