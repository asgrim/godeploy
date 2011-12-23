<?php

class AuthControllerTest extends ControllerTestCase
{
	public function testLoginFormExists()
	{
		$this->dispatch('/auth/login');
		$this->assertResponseCode(200);
		$this->assertQuery('input#username');
		$this->assertQuery('input#password');
	}

	public function testPostRequestToLoginFormLogsInAndRedirects()
	{
		$post = array(
			'username' => TEST_USER,
			'password' => TEST_PASSWORD,
		);
		$this->request->setMethod('POST')->setPost($post);

		$this->dispatch('/auth/login');
		$this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
		$this->assertRedirectTo('/home');
	}

	public function testPostRequestWithIncorrectLoginDoesNotWork()
	{
		$post = array(
			'username' => TEST_USER,
		);
		$this->request->setMethod('POST')->setPost($post);

		$this->dispatch('/auth/login');
		$this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
		$this->assertNotRedirect();
		$this->assertController('auth');
		$this->assertAction('login');
		$this->assertQuery('div#errors');
	}

	public function testLogoutRedirect()
	{
		$this->loginUser();
		$this->dispatch('/auth/logout');
		$this->assertRedirectTo("/auth/login");
	}

	public function testAuthControllerLogoutFunctionality()
	{
		$this->loginUser();

		$this->dispatch('/');

		$auth_controller = new AuthController($this->request, $this->response, $this->request->getParams());
		$executeLogoutMethod = self::getPrivateMethod('executeLogout', 'AuthController');
		$executeLogoutMethod->invokeArgs($auth_controller, array());

		$auth = Zend_Auth::getInstance();
		$this->assertFalse($auth->hasIdentity());
	}

	public function testGetAuthAdapterFunctionReturnsAuthAdapter()
	{
		$this->dispatch('/');

		$auth_controller = new AuthController($this->request, $this->response, $this->request->getParams());
		$return_value = $auth_controller->getAuthAdapter(array('username'=>'','password'=>''));

		$this->assertTrue($return_value instanceof Zend_Auth_Adapter_Interface);
	}
}
