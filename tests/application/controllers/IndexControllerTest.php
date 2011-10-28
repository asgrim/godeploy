<?php

class IndexControllerTest extends ControllerTestCase
{
	public function testIndexRedirectWorks()
	{
		$this->dispatch('/');

		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
		{
			$this->assertRedirectTo("/home");
		}
		else
		{
			$this->assertRedirectTo("/auth/login");
		}
	}
}
