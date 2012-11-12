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

	public function dispatch($url = null)
	{
		parent::dispatch($url);
	}

	public function loginUser()
	{
		$adapter = new GD_Auth_Database(TEST_USER, TEST_PASSWORD);
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

	/**
	 * Return a ReflectionMethod of a private/protected method and make it
	 * public for testing purposes
	 *
	 * @param string $method Name of the method
	 * @param string $class Name of the class the method contains
	 * @return ReflectionMethod
	 */
	protected static function getPrivateMethod($method, $class)
	{
		$class = new ReflectionClass($class);
		$method = $class->getMethod($method);
		$method->setAccessible(true);
		return $method;
	}

	/**
	 * @param string $expectedCode
	 * @param string $message
	 */
	public function assertResponseCode($expectedCode, $message = '')
	{
		$this->assertEquals($expectedCode, $this->getResponse()->getHttpResponseCode(), $message);
	}

	/**
	 * @param string $expectedUrl
	 * @param string $message
	 */
	public function assertRedirectTo($expectedUrl, $message = '')
	{
		$headers = $this->getResponse()->getHeaders();
		$actualUrl = '';

		foreach($headers as $header)
		{
			if ($header['name'] === 'Location')
			{
				$actualUrl = $header['value'];
			}
		}

		$this->assertEquals($expectedUrl, $actualUrl, $message);
	}

	public function assertDomQuery($path, $message = '')
	{
		$content = $this->response->outputBody();
		$domQuery = new Zend_Dom_Query($content);
		$result = $domQuery->query($path);

		$this->assertGreaterThan(0, count($result), $message);
	}
}
