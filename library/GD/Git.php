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
 * Git wrapper, unwritten yet...
 * @author james
 *
 */
class GD_Git extends MAL_Util_Shell
{
	private $_url;
	private $_project;
	private $_gitdir;
	private $_current_branch;
	private $_repotype;
	private $_apache_home;

	private $_base_gitdir;

	const GIT_REPOTYPE_SSH = 'ssh'; // SSH (read/write)
	const GIT_REPOTYPE_HTTP = 'http'; // HTTP (read/write)
	const GIT_REPOTYPE_GIT = 'git'; // Git Read-Only

	const GIT_CLONE_ERROR_ALREADY_CLONED = "CLONE_ALREADY_CLONED";
	const GIT_CLONE_ERROR_HOST_KEY_FAILURE = "CLONE_HOST_KEY_FAILURE";
	const GIT_CLONE_ERROR_UNKNOWN = "CLONE_UNKNOWN_ERROR";
	const GIT_CLONE_ERROR_REMOTE_OTHER = "CLONE_REMOTE_OTHER";
	const GIT_CLONE_ERROR_REMOTE_NOT_FOUND = "CLONE_REMOTE_NOT_FOUND";

	const GIT_PULL_ERROR_UNKNOWN = "PULL_UNKNOWN_ERROR";

	const GIT_STATUS_ERROR_NOT_ON_BRANCH = "STATUS_NOT_ON_BRANCH";
	const GIT_STATUS_ERROR_UNKNOWN = "STATUS_UNKNOWN_ERROR";
	const GIT_STATUS_ERROR_DIFFERENT_REPOSITORY = "STATUS_DIFFERENT_REPOSITORY";
	const GIT_STATUS_ERROR_NOT_A_REPOSITORY = "STATUS_NOT_A_REPOSITORY";

	const GIT_GENERAL_ERROR = "GENERAL_GIT_ERROR";
	const GIT_GENERAL_EMPTY_REF = "EMPTY_REF";
	const GIT_GENERAL_INVALID_REF = "INVALID_REF";
	const GIT_GENERAL_NO_FILES_CHANGED = "NO_FILES_CHANGED";

	const GIT_SSH_ERROR_HOSTNME = "SSH_RESOLVE_HOSTNAME";
	const GIT_SSH_ERROR_UNKNOWN = "SSH_UNKNOWN_ERROR";

	public function __construct(GD_Model_Project &$project)
	{
		$this->_project = $project;
		$this->_base_gitdir = APPLICATION_PATH . "/../gitcache/";;
		$this->_gitdir = $this->_base_gitdir . $this->_project->getId();
		$this->_url = $this->_project->getRepositoryUrl();
		$this->_repotype = $this->parseRepoType($this->_url);
		$this->_apache_home = getenv('HOME');

		if($this->_apache_home == "")
		{
			throw new GD_Exception("Apache user '" . getenv('APACHE_RUN_USER') . "' directory not exported. Try export HOME=???");
		}

		if($this->_repotype == self::GIT_REPOTYPE_HTTP)
		{
			throw new GD_Exception("Repository type HTTP/HTTPS not supported yet.");
		}
		/*if($this->_repotype == self::GIT_REPOTYPE_SSH)
		{
			throw new GD_Exception("Repository type Git (read+write via SSH) not supported yet.");
		}*/

		if(!file_exists($this->_base_gitdir))
		{
			mkdir($this->_base_gitdir, 0700, true);
			chdir($this->_base_gitdir);
		}

		// Check out the specified branch in the project if we're a valid repo
		try
		{
			if($this->checkValidRepository())
			{
				$this->gitCheckout($project->getDeploymentBranch());
			}
		}
		catch(GD_Exception $ex)
		{
			$invalid = true;
		}

		$this->_current_branch = $this->getCurrentBranch(true);
	}

	private function sshKeys()
	{
		if($this->_repotype == self::GIT_REPOTYPE_SSH)
		{
			// Write the id_rsa key to the apache home directory
			$id_rsa = $this->_project->getSSHKey()->getPrivateKey();

			$keyfile = $this->_apache_home . "/.ssh/id_rsa";

			if(file_exists($keyfile))
			{
				unlink($keyfile);
			}
			file_put_contents($keyfile, $id_rsa);
			chmod($keyfile, 0600);

			// Get the hostname part of the URL
			$x = strrchr($this->_url, ':');
			$host = substr($this->_url, 0, -strlen($x));
			$host = preg_replace("/[^@0-9a-zA-Z-_.]/", "", $host);

			// Test the connection
			$this->runShell("ssh -T -o StrictHostKeyChecking=no {$host}", false);

			if($this->_last_errno != 0)
			{
				// First check if we're a Github sort of repo
				// Github returns: Hi [USER]! You've successfully authenticated, but GitHub does not provide shell access.
				// Codebase returns: You've successfully uploaded your public key to Codebase and authenticated.
				$valid_string = "You've successfully";
				$is_valid = false;
				foreach($this->_last_output as $o)
				{
					if(strpos($o, $valid_string) !== false)
					{
						return;
					}
				}

				if(in_array("ERROR:gitosis.serve.main:Need SSH_ORIGINAL_COMMAND in environment.", $this->_last_output))
				{
					/*
					 * This is actually a correct response - default gitosis setup will serve up one of these:
					 *
					 * PTY allocation request failed on channel 0
					 * ERROR:gitosis.serve.main:Need SSH_ORIGINAL_COMMAND in environment.
					 *
					 * or just
					 *
					 * ERROR:gitosis.serve.main:Need SSH_ORIGINAL_COMMAND in environment.
					 *
					 */
					return;
				}
				else if(strpos($this->_last_output[0], "Could not resolve hostname") !== false
						|| (isset($this->_last_output[1]) && strpos($this->_last_output[1], "Could not resolve hostname") !== false))
				{
					throw new GD_Exception("Could not resolve hostname '{$host}'", 0, self::GIT_SSH_ERROR_HOSTNME);
				}
				else
				{
					$final_error = end($this->_last_output);
					throw new GD_Exception("Tried setting up SSH authentication but failed. Final error was: {$final_error}", 0, self::GIT_SSH_ERROR_UNKNOWN);
				}
			}
		}
	}

	private function parseRepoType($url)
	{
		if(substr($url, 0, 6) == "git://")
		{
			return self::GIT_REPOTYPE_GIT;
		}
		else if(substr($url, 0, 8) == "https://")
		{
			return self::GIT_REPOTYPE_HTTP;
		}
		else
		{
			return self::GIT_REPOTYPE_SSH;
		}
	}

	public function getGitDir()
	{
		return $this->_gitdir . "/";
	}

	private function deleteFolderRecursively($path)
	{
		if(is_dir($path))
		{
			$ls = scandir($path);
			foreach($ls as $i)
			{
				if($i != "." && $i != "..")
				{
					$pathi = $path . "/" . $i;
					if(filetype($pathi) == "dir")
					{
						$this->deleteFolderRecursively($pathi);
					}
					else
					{
						unlink($pathi);
					}
				}
			}
			reset($ls);
			rmdir($path);
		}
		else
		{
			unlink($path);
		}
	}

	public function deleteRepository()
	{
		$this->deleteFolderRecursively($this->_gitdir);
	}

	public function getCurrentBranch($silent = false)
	{
		$this->runShell('git status');

		if($this->_last_errno == 0)
		{
			if($this->_last_output[0] == "# Not currently on any branch.")
			{
				if(!$silent)
				{
					throw new GD_Exception("Git repository for {$this->_project->getName()} was not on a branch.", self::GIT_STATUS_ERROR_NOT_ON_BRANCH);
				}
			}
			else if(preg_match("/# On branch ([a-zA-Z0-9-.]*)/", $this->_last_output[0], $matches))
			{
				return $matches[1];
			}
			else
			{
				throw new GD_Exception("Unhandled error in getCurrentBranch", self::GIT_STATUS_ERROR_UNKNOWN);
			}
		}
		else
		{
			if(!$silent)
			{
				throw new GD_Exception("Git status did not work.", self::GIT_STATUS_ERROR_UNKNOWN);
			}
		}
	}

	public function gitCheckout($ref)
	{
		$this->runShell('git checkout ' . $this->sanitizeRef($ref));

		if($this->_last_errno == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function parsePrettyOneline($line)
	{
		$raw_commit_info = explode(" ", $line, 2);
		$commit_info = array();
		$commit_info["HASH"] = $raw_commit_info[0];
		$commit_info["MESSAGE"] = $raw_commit_info[1];
		return $commit_info;
	}

	private function getSingleLog($cmd)
	{
		$this->runShell($cmd);

		if($this->_last_errno == 0)
		{
			return $this->parsePrettyOneline($this->_last_output[0]);
		}
		else
		{
			return self::GIT_GENERAL_ERROR;
		}
	}

	public function getLastCommit()
	{
		return $this->getSingleLog('git log -n1 --pretty=oneline');
	}

	public function getFirstCommit()
	{
		return $this->getSingleLog('git log --pretty=oneline | tail -1');
	}

	public function getFullHash($ref)
	{
		$nice_ref = $this->sanitizeRef($ref);

		if($nice_ref == "")
		{
			throw new GD_Exception("Could not get full hash '{$nice_ref}': " . self::GIT_GENERAL_EMPTY_REF, 0, self::GIT_GENERAL_EMPTY_REF);
		}

		$this->runShell('git log -n1 --format="format:%H" ' . $nice_ref);

		if($this->_last_errno == 0)
		{
			return $this->_last_output[0];
		}
		else
		{
			throw new GD_Exception("Could not get full hash '{$nice_ref}': " . self::GIT_GENERAL_INVALID_REF, 0, self::GIT_GENERAL_INVALID_REF);
		}
	}

	public function getFilesChangedList($from_rev, $to_rev)
	{
		if($from_rev == "")
		{
			// File list is EVERY file at the $to_rev, so we have to be a bit dirty here :(

			// Get the current branch and store it
			$previous_ref = $this->getCurrentBranch(true);

			// Checkout the $to_rev
			$this->gitCheckout($to_rev);

			// Get a list of all the files recursively
			$this->runShell('git ls-tree --full-tree -r --name-status ' . $to_rev);
			$files = array();
			foreach($this->_last_output as $f)
			{
				if(strpos($f, '.gitignore') === false)
				{
					$files[] = "A\t" . $f;
				}
			}

			// Go back to previous branch
			$this->gitCheckout($previous_ref);
		}
		else
		{
			$this->runShell('git diff --name-status ' . $this->sanitizeRef($from_rev) . '..' . $this->sanitizeRef($to_rev));
			$files = array();
			foreach($this->_last_output as $f)
			{
				if(strpos($f, '.gitignore') === false)
				{
					$files[] = $f;
				}
			}
		}

		if(!is_array($files) || count($files) <= 0)
		{
			throw new GD_Exception("Could not get file list... could be that there was no changes.", 0, self::GIT_GENERAL_NO_FILES_CHANGED);
		}

		// Now parse the file list into something sensible
		$file_list = array();
		foreach($files as &$f)
		{
			$stuff = explode("\t", $f, 2);
			$file_list[] = array("action" => $stuff[0], "file" => $stuff[1]);
		}
		return $file_list;
	}

	public function gitPull($branch = "master", $remote = "origin")
	{
		// TODO - Clean arguments (only accept valid branch/remote characters)
		$this->sshKeys();
		$this->runShell('git pull ' . $remote . ' ' . $branch);

		if($this->_last_errno == 0)
		{
			$this->runShell('git fetch --tags ' . $remote);

			if($this->_last_errno == 0)
			{
				return true;
			}
			else
			{
				return self::GIT_PULL_ERROR_UNKNOWN;
			}
		}
		else
		{
			return self::GIT_PULL_ERROR_UNKNOWN;
		}
	}

	public function gitCloneOrPull()
	{
		$clone_error = $this->gitClone();
		if($clone_error == self::GIT_CLONE_ERROR_ALREADY_CLONED)
		{
			return $this->gitPull();
		}
		else
		{
			return $clone_error;
		}
	}

	public function gitClone()
	{
		$this->sshKeys();
		$this->runShell('git clone ' . $this->_url . ' "' . $this->_gitdir . '"', true);

		if($this->_last_errno == 0)
		{
			$this->runShell('git reset --hard HEAD', true);
			$this->runShell('git config core.filemode false', true);
			return true;
		}
		else
		{
			if($this->_last_output[0] == "fatal: destination path '{$this->_gitdir}' already exists and is not an empty directory.")
			{
				return self::GIT_CLONE_ERROR_ALREADY_CLONED;
			}
			if($this->_last_output[0] == "Initialized empty Git repository in {$this->_gitdir}"
				&& $this->_last_output[1] == "Host key verification failed.")
			{
				return self::GIT_CLONE_ERROR_HOST_KEY_FAILURE;
			}
			if($this->_last_output[0] == "fatal: remote error:")
			{
				if(stripos($this->_last_output[1], "Could not find Repository") !== false)
				{
					return self::GIT_CLONE_ERROR_REMOTE_NOT_FOUND;
				}
				else
				{
					return self::GIT_CLONE_ERROR_REMOTE_OTHER;
				}
			}
			return self::GIT_CLONE_ERROR_UNKNOWN;
		}
	}

	public function checkValidRepository()
	{
		$this->runShell('git remote -v | grep origin | grep fetch', true);

		if($this->_last_errno == 0)
		{
			$actual_url = $this->_last_output[0];
			$actual_url = str_replace("origin", "", $actual_url);
			$actual_url = str_replace("(fetch)", "", $actual_url);
			$actual_url = trim($actual_url);

			if($actual_url != $this->_url)
			{
				throw new GD_Exception("Repository cache does not match the project's URL", 0, self::GIT_STATUS_ERROR_DIFFERENT_REPOSITORY);
			}

			return true;
		}
		else
		{
			throw new GD_Exception("Not a git repository", 0, self::GIT_STATUS_ERROR_NOT_A_REPOSITORY);
		}

		throw new GD_Exception("Unknown error", 0, self::GIT_STATUS_ERROR_UNKNOWN);
	}

	private function runShell($cmd, $chdir = true, $noisy = false)
	{
		if($chdir)
		{
			if(!file_exists($this->_gitdir))
			{
				mkdir($this->_gitdir, 0700, true);
			}
			chdir($this->_gitdir);
		}

		parent::Exec($cmd, $noisy);
	}

	private function sanitizeRef($ref)
	{
		$new_ref = $ref;
		$new_ref = preg_replace("/[^-\/a-zA-Z0-9_-]/", "", $new_ref);
		return $new_ref;
	}
}