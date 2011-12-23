<?php

class HomeControllerTest extends ControllerTestCase
{
	public function testHomePageContent()
	{
		$this->loginUser();
		$this->dispatch('/home');
		$this->assertResponseCode(200);
		$this->assertQuery('a.add_icon');
	}
}
