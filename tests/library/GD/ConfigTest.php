<?php

class GD_ConfigTest extends GD_TestCase
{
	public function testCanGetAndSetConfig()
	{
		$this->loadDatabaseFromConfig();

		$checkKey = "gd_unit_test_value";
		$checkValue = time();

		$retval = GD_Config::set($checkKey, $checkValue);

		if(is_numeric($retval))
		{
			$this->assertGreaterThan(0, $retval);
		}
		else
		{
			$this->assertTrue($retval);
		}

		$retval2 = GD_Config::get($checkKey);

		$this->assertEquals($checkValue, $retval2);
	}
}