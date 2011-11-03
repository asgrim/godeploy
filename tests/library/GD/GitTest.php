<?php

class GD_GitTest extends GD_TestCase
{
	/**
	 * To set up SSH unit tests, you need to ssh-keygen an rsa key, and save it
	 * as e.g. /home/godeploy/tests/id_rsa an upload the id_rsa.pub to github
	 *
	 * @return string or false on failure
	 */
	private function getRSAKey()
	{
		$rsa_file = realpath(APPLICATION_PATH . "/../tests/id_rsa");
		if(file_exists($rsa_file))
		{
			return file_get_contents($rsa_file);
		}
		return false;
	}

	public function testGitDirLooksCorrect()
	{
		$git = new GD_Git("unittest1", "git://github.com/asgrim/godeploy-test-project.git", "master", "");

		$this->assertNotEquals("/", $git->getGitDir());
		$this->assertContains("gitcache", $git->getGitDir());

		$git->deleteRepository();
	}

	public function testGitCloneContainsFirstCommit()
	{
		$git = new GD_Git("unittest1", "git://github.com/asgrim/godeploy-test-project.git", "master", "");
		$git->deleteRepository();
		$git->gitClone();

		$first_commit = $git->getFirstCommit();

		$this->assertEquals("381164ffebefeacfce47091becf5dc94244616a7", $first_commit["HASH"]);

		$git->deleteRepository();
	}

	public function testGitSSHCommand()
	{
		$rsa_key = $this->getRSAKey();

		if($rsa_key === false)
		{
			$this->markTestSkipped("RSA key not available, skipping Git SSH test");
			return;
		}

		if(!defined('GITHUB_USER') || GITHUB_USER == "")
		{
			$this->markTestSkipped("Github user not set up, cannot test SSH");
			return;
		}

		$git = new GD_Git("unittest2", "git@github.com:" . GITHUB_USER . "/godeploy-test-project.git", "master", "");
	}

	public function testGitSSHClone()
	{
		$rsa_key = $this->getRSAKey();

		if($rsa_key === false)
		{
			$this->markTestSkipped("RSA key not available, skipping Git SSH test");
			return;
		}

		if(!defined('GITHUB_USER') || GITHUB_USER == "")
		{
			$this->markTestSkipped("Github user not set up, cannot test SSH");
			return;
		}

		$git = new GD_Git("unittest2", "git@github.com:" . GITHUB_USER . "/godeploy-test-project.git", "master", "");
		$git->deleteRepository();

		$result = $git->gitClone();
		$this->assertTrue($result);

		$git->deleteRepository();
	}
}