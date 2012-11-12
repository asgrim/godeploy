<?php

class DeployControllerTest extends ControllerTestCase
{
	public function testDeployToLink()
	{
		$this->loginUser();
		$project = $this->createTestProject();
		$this->cloneTestProject($project);

		$test_string = '__phpunit_testing__';

		$dispatch_url = '/project/' . $project->getSlug() . '/deploy?to=' . $test_string;

		$this->dispatch($dispatch_url);

		$this->assertResponseCode(200);
		$this->assertDomQuery("input#toRevision");

		$results = $this->getDomQuery("input#toRevision");
		$this->assertCount(1, $results, 'Expected only one element with id="toRevision"');
		$this->assertEquals($test_string, $results->rewind()->getAttribute("value"));

		$this->deleteTestProject($project);
	}
}