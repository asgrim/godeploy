<?php

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
	public function setUp()
	{
		$this->bootstrap = new Zend_Application(
			APPLICATION_ENV,
			APPLICATION_PATH . '/configs/system.ini'
		);
		parent::setUp();
	}

	public function tearDown()
	{
		Zend_Controller_Front::getInstance()->resetInstance();
		$this->resetRequest();
		$this->resetResponse();

		$this->request->setPost(array());
		$this->request->setQuery(array());
	}

	public function dispatch($url)
	{
		parent::dispatch($url);
	}

	public function loginUser()
	{
		$adapter = new GD_Auth_Database("testuser", "testpassword");
		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($adapter);

		$message = "";
		switch($result->getCode())
		{
			case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
				$message = "Credential invalid";
				break;
			case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
				$message = "Identity ambiguous";
				break;
			case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
				$message = "Identity not found";
				break;
			case Zend_Auth_Result::FAILURE:
				$message = "Failure";
				break;
		}

		$this->assertTrue($auth->hasIdentity(), "Failed to login. message='{$message}'");
	}
}
