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
		$auth_controller->executeLogout();

		$auth = Zend_Auth::getInstance();
		$this->assertFalse($auth->hasIdentity());
	}
}
