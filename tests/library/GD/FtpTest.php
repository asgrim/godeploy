<?php

class GD_FtpTest extends GD_TestCase
{
	public function setUp()
	{
		if(!defined('TEST_FTP_HOSTNAME')
				|| !defined('TEST_FTP_USERNAME')
				|| !defined('TEST_FTP_PASSWORD')
				|| !defined('TEST_FTP_PORT')
				|| !defined('TEST_FTP_REMOTE_PATH'))
		{
			$this->markTestSkipped("Not all FTP details not setup, cannot test GD_Ftp class");
			return;
		}
	}

	public function testCanCreateFtpClass()
	{
		$ftp = new GD_Ftp(TEST_FTP_HOSTNAME, TEST_FTP_USERNAME, TEST_FTP_PASSWORD, TEST_FTP_REMOTE_PATH, TEST_FTP_PORT);

		$this->assertInstanceOf('GD_Ftp', $ftp);
	}

	/**
	 * @depends testCanCreateFtpClass
	 */
	public function testCanConnect()
	{
		$ftp = new GD_Ftp(TEST_FTP_HOSTNAME, TEST_FTP_USERNAME, TEST_FTP_PASSWORD, TEST_FTP_REMOTE_PATH, TEST_FTP_PORT);

		try
		{
			$ftp->connect();
		}
		catch(GD_Exception $ex)
		{
			$this->fail("Failed to connect to FTP server - {$ex->getMessage()}");
		}
	}
}
