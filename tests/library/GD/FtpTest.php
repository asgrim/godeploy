<?php

class GD_FtpTest extends GD_TestCase
{
	public function testCanCreateFtpClass()
	{
		$ftp = new GD_Ftp(new GD_Model_Server());

		$this->assertTrue($ftp instanceof GD_Ftp);
	}
}