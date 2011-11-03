<?php

class GD_Model_ConnectionTypeTest extends GD_TestCase
{
	public function testIdGetAndSet()
	{
		$obj = new GD_Model_ConnectionType();
		$obj->setId(5);

		$this->assertSame(5, $obj->getId());
	}

	public function testNameGetAndSet()
	{
		$obj = new GD_Model_ConnectionType();
		$obj->setName("Test");

		$this->assertSame("Test", $obj->getName());
	}

	public function testDefaultPortGetAndSet()
	{
		$obj = new GD_Model_ConnectionType();
		$obj->setId(21);

		$this->assertSame(21, $obj->getId());
	}
}
