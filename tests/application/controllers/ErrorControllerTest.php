<?php

class ErrorControllerTest extends ControllerTestCase
{
	public function test404Response()
	{
		$this->dispatch('/error/404');
		$this->assertResponseCode(404);
	}

	public function test404RedirectResponseGuest()
	{
		$this->dispatch('/doesnt/exist');
		$this->assertRedirectTo("/auth/login");
	}

	public function test404RedirectResponseLoggedIn()
	{
		$this->loginUser();
		$this->dispatch('/ThisTestWillObviouslyFailAsThisPageDoesntExist');
		$this->assertResponseCode(404);
	}
}
