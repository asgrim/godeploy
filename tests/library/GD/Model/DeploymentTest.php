<?php

class GD_Model_DeploymentTest extends GD_TestCase
{
	protected $_users_id;
	protected $_projects_id;
	protected $_when;
	protected $_servers_id;
	protected $_from_revision;
	protected $_to_revision;
	protected $_comment;
	protected $_deployment_statuses_id;

	public function testIdGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setId(5);

		$this->assertSame(5, $obj->getId());
	}

	public function testUsersIdGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setUsersId(5);

		$this->assertSame(5, $obj->getUsersId());
	}

	public function testProjectsIdGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setProjectsId(5);

		$this->assertSame(5, $obj->getProjectsId());
	}

	public function testWhenGetAndSet()
	{
		$value = date("Y-m-d H:i:s");

		$obj = new GD_Model_Deployment();
		$obj->setWhen($value);

		$this->assertSame($value, $obj->getWhen("Y-m-d H:i:s"));
	}

	public function testServersIdGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setServersId(5);

		$this->assertSame(5, $obj->getServersId());
	}

	public function testFromRevisionGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setFromRevision("Test");

		$this->assertSame("Test", $obj->getFromRevision());
	}

	public function testToRevisionGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setToRevision("Test");

		$this->assertSame("Test", $obj->getToRevision());
	}

	public function testCommentGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setComment("Test");

		$this->assertSame("Test", $obj->getComment());
	}

	public function testDeploymentStatusesIdGetAndSet()
	{
		$obj = new GD_Model_Deployment();
		$obj->setDeploymentStatusesId(5);

		$this->assertSame(5, $obj->getDeploymentStatusesId());
	}
}
