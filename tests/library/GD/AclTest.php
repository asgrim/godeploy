<?php

class GD_AclTest extends GD_TestCase
{
	public function testGDAclIsAZendAcl()
	{
		$acl = new GD_Acl();

		$this->assertTrue($acl instanceof Zend_Acl);
	}

	public function testAclCreatesExpectedRoles()
	{
		$acl = new GD_Acl();

		$roles = $acl->getRoles();

		$this->assertContains("guest", $roles);
		$this->assertContains("member", $roles);
		$this->assertContains("admin", $roles);
	}

	/**
	 * Although this doesn't check the existence of every single resource, it
	 * will check that at least some of the core resources have been set
	 */
	public function testAclCreatesExpectedCoreResources()
	{
		$acl = new GD_Acl();

		$resources = $acl->getResources();

		$this->assertContains("index", $resources);
		$this->assertContains("error", $resources);
		$this->assertContains("auth", $resources);
		$this->assertContains("home", $resources);
		$this->assertContains("admin", $resources);
		$this->assertContains("setup", $resources);
	}
}