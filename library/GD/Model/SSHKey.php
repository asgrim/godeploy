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
class GD_Model_SSHKey
{
	protected $_id;
	protected $_ssh_key_types_id;
	protected $_private_key;
	protected $_public_key;
	protected $_comment;

	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setSSHKeyTypesId($id)
	{
		$this->_ssh_key_types_id = (int)$id;
		return $this;
	}

	public function getSSHKeyTypesId()
	{
		return $this->_ssh_key_types_id;
	}

	public function setPrivateKey($value)
	{
		$this->_private_key = (string)$value;
		return $this;
	}

	public function getPrivateKey()
	{
		return $this->_private_key;
	}

	public function setPublicKey($value)
	{
		$this->_public_key = (string)$value;
		return $this;
	}

	public function getPublicKey()
	{
		return $this->_public_key;
	}

	public function setComment($value)
	{
		$this->_comment = (string)$value;
		return $this;
	}

	public function getComment()
	{
		return $this->_comment;
	}

	public function generateKeyPair()
	{
		$comment = "godeploy@" . gethostname();

		$filename = sys_get_temp_dir() . "/ssh_keygen_pair" . md5(microtime());

		$ds = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "a"),
		);
		$cmd = 'ssh-keygen -t rsa -C "' . $comment . '" ';

		$pid = proc_open($cmd, $ds, $pipes);
		if(is_resource($pid))
		{
			fwrite($pipes[0], "{$filename}\n");
			fwrite($pipes[0], "\n");
			fwrite($pipes[0], "\n");
			fclose($pipes[0]);

			$output = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$return_value = proc_close($pid);
		}
		else
		{
			throw new GD_Exception("Failed to start ssh-keygen to generate ssh key pair.");
		}

		if($return_value == 0)
		{
			$id_rsa = file_get_contents($filename);
			$id_rsa_pub = file_get_contents($filename . ".pub");
			unlink($filename);
			unlink($filename . ".pub");

			$this->setPrivateKey($id_rsa);
			$this->setPublicKey($id_rsa_pub);
			$this->setComment($comment);
			$this->setSSHKeyTypesId(1);
		}
		else
		{
			throw new GD_Exception("Failed to generate ssh key pair: " . nl2br($output));
		}
	}
}
