<?php

class ErrorControllerTest extends ControllerTestCase
{
	public function test404Response()
	{
		$this->dispatch('/error/privilege');
		$this->assertResponseCode(404);
	}

	public function test404RedirectResponseGuest()
	{
		$this->dispatch('/IfThisPageExistsUnitTestsWillFail');
		$this->assertRedirectTo("/auth/login");
	}

	public function test404RedirectResponseLoggedIn()
	{
		$this->loginUser();
		$this->dispatch('/IfThisPageExistsUnitTestsWillFail');
		$this->assertResponseCode(404);
	}
}
