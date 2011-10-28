<?php

	class GD_GitTest extends PHPUnit_Framework_TestCase
	{
		public function getMockProject()
		{
			$project = new GD_Model_Project();
			return $project;
		}


		public function getMockGit()
		{
			$proj = $this->getMockProject();
			$git = new GD_Git($proj);
			return $git;
		}

		public function testGitDirLooksCorrect()
		{
			$git = $this->getMockGit();

			$this->assertNotEquals("/", $git->getGitDir());
			$this->assertContains("gitcache", $git->getGitDir());
		}
	}