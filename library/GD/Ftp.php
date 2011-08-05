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

/**
 * FTP wrapper
 * @author james
 *
 */
class GD_Ftp
{
	private $_hostname;
	private $_username;
	private $_password;
	private $_port;
	private $_remote_path;

	private $_handle;
	private $_pwd;

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

	private function resetPwd()
	{
		ftp_chdir($this->_handle, $this->_pwd);
	}

	private function getRemotePath($filename)
	{
		if(!$this->_handle)
		{
			throw new GD_Exception("Must be connected to get remote path.");
		}

		$filename_has_leading_slash = false;
		$remote_path_has_trailing_slash = false;
		$remote_path_has_leading_slash = false;

		if(substr($filename, 0, 1) == "/")
		{
			$filename_has_leading_slash = true;
		}

		if(substr($this->_remote_path, strlen($this->_remote_path) - 1, 1) == "/")
		{
			$remote_path_has_trailing_slash = true;
		}

		if(substr($this->_remote_path, 0, 1) == "/")
		{
			$remote_path_has_leading_slash = true;
		}

		$use_remote_path = $this->_remote_path;

		if($remote_path_has_trailing_slash && $filename_has_leading_slash)
		{
			$use_remote_path = substr($use_remote_path, 0, strlen($this->_remote_path) - 1);
		}
		else if(!$remote_path_has_trailing_slash && !$filename_has_leading_slash)
		{
			$use_remote_path = $use_remote_path . "/";
		}

		if(!$remote_path_has_leading_slash)
		{
			$use_remote_path = $this->_pwd . "/" . $use_remote_path;
		}

		return $use_remote_path . $filename;
	}

	public function testConnection()
	{
		try
		{
			$this->connect();

			$test_content = "Some test content...";
			$test_file = tempnam(sys_get_temp_dir(), "gd_upload_test_");
			$remote_test_file = $this->getRemotePath(".gd_test_file");
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
			return false;
		}
	}

	private function ftpChangeOrMakeDirectory($dir)
	{
		if($dir == "" || @ftp_chdir($this->_handle, $dir) || @ftp_mkdir($this->_handle, $dir))
		{
			return true;
		}
		if(!$this->ftpChangeOrMakeDirectory(dirname($dir)))
		{
			return false;
		}
		return ftp_mkdir($this->_handle, $dir);
	}

	public function upload($local_file, $remote_file)
	{
		$remote_dir = str_replace(basename($remote_file), "", $remote_file);
		$this->ftpChangeOrMakeDirectory($remote_dir);

		echo "Uploading '{$local_file}' to {$remote_file}<br />";
		if(!ftp_put($this->_handle, $remote_file, $local_file, FTP_BINARY))
		{
			throw new GD_Exception("Failed to upload '{$local_file}'");
		}

		//@ftp_chmod($this->_handle, 0777, $remote_file);

		$this->resetPwd();
	}

	public function delete($remote_file)
	{
		$remote_dir = str_replace(basename($remote_file), "", $remote_file);
		$this->ftpChangeOrMakeDirectory($remote_dir);

		if(!ftp_delete($this->_handle, basename($remote_file)))
		{
			throw new GD_Exception("Failed to delete '{$remote_file}'");
		}

		$this->resetPwd();
	}

	public function connect()
	{
		$this->_handle = ftp_connect($this->_hostname, $this->_port);

		if($this->_handle == false)
		{
			throw new GD_Exception("Couldn't connect to FTP server on '{$this->_hostname}:{$this->_port}'");
		}

		if(!ftp_login($this->_handle, $this->_username, $this->_password))
		{
			throw new GD_Exception("Failed to log in to '{$this->_hostname}:{$this->_port}' with user '{$this->_username}'");
		}

		$this->_pwd = ftp_pwd($this->_handle);
	}

	public function disconnect()
	{
		ftp_close($this->_handle);
	}
}