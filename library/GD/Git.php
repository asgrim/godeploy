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
class GD_Git
{
	private $_url;
	private $_project;
	private $_gitdir;
	private $_current_branch;

	private $_last_output;
	private $_last_errno;

	private $_base_gitdir;

	const GIT_CLONE_ERROR_ALREADY_CLONED = "CLONE_ALREADY_CLONED";
	const GIT_CLONE_ERROR_HOST_KEY_FAILURE = "CLONE_HOST_KEY_FAILURE";
	const GIT_CLONE_ERROR_UNKNOWN = "CLONE_UNKNOWN_ERROR";

	const GIT_PULL_ERROR_UNKNOWN = "PULL_UNKNOWN_ERROR";

	const GIT_STATUS_ERROR_NOT_ON_BRANCH = "STATUS_NOT_ON_BRANCH";
	const GIT_STATUS_ERROR_UNKNOWN = "STATUS_UNKNOWN_ERROR";

	const GIT_GENERAL_ERROR = "GENERAL_GIT_ERROR";

	public function __construct(GD_Model_Project &$project)
	{
		$this->_project = $project;
		$this->_base_gitdir = APPLICATION_PATH . "/../gitcache/";;
		$this->_gitdir = $this->_base_gitdir . $this->_project->getId();

		if(!file_exists($this->_base_gitdir))
		{
			mkdir($this->_base_gitdir, 0700, true);
		}

		$this->_current_branch = $this->getCurrentBranch(true);
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

	public function getLastCommit()
	{
		$this->runShell('git log -n1 --pretty=oneline');

		if($this->_last_errno == 0)
		{
			$raw_commit_info = explode(" ", $this->_last_output[0], 2);
			$commit_info = array();
			$commit_info["HASH"] = $raw_commit_info[0];
			$commit_info["MESSAGE"] = $raw_commit_info[1];
			return $commit_info;
		}
		else
		{
			return self::GIT_GENERAL_ERROR;
		}
	}

	public function gitPull($branch = "master", $remote = "origin")
	{
		// TODO - Clean arguments (only accept valid branch/remote characters)
		$this->runShell('git pull ' . $remote . ' ' . $branch);

		if($this->_last_errno == 0)
		{
			return true;
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
		$this->runShell('git clone ' . $this->_project->getRepositoryUrl() . ' "' . $this->_gitdir . '"', false);

		if($this->_last_errno == 0)
		{
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
			return self::GIT_CLONE_ERROR_UNKNOWN;
		}
	}

	private function runShell($cmd, $chdir = true, $noisy = false)
	{
		if($chdir)
		{
			chdir($this->_gitdir);
		}

		if($noisy) echo "<strong>" . $cmd . "</strong><br /><br />";
		$this->_last_errno = 0;
		$this->_last_output = array();
		exec($cmd . " 2>&1", $this->_last_output, $this->_last_errno);
		if($noisy)
		{
			echo "<pre>";
			var_dump($this->_last_output);
			var_dump($this->_last_errno);
			echo "</pre>";
			echo "<hr />";
		}
	}
}