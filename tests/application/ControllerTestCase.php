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
	 * @return GD_Model_Project
	 */
	public function createTestProject()
	{
		$projects = new GD_Model_ProjectsMapper();
		$project = new GD_Model_Project();
		$project->setName("Unit Test Project");
		$project->setDeploymentBranch('master');
		$project->setRepositoryTypesId(1);
		$project->setRepositoryUrl('git://github.com/asgrim/godeploy-test-project.git');
		$project->setSSHKeysId(1);
		$projects->save($project);

		$servers = new GD_Model_ServersMapper();
		$server = new GD_Model_Server();
		$server->setProjectsId($project->getId());
		$server->setConnectionTypesId(1);
		$server->setHostname("localhost");
		$server->setName("Unit Test Server");
		$servers->save($server);

		return $project;
	}

	public function cloneTestProject(GD_Model_Project $project)
	{
		$git = GD_Git::FromProject($project);
		$git->gitCloneOrPull();
	}

	public function deleteTestProject(GD_Model_Project $project)
	{
		$git = GD_Git::FromProject($project);
		$git->deleteRepository();

		$servers_map = new GD_Model_ServersMapper();
		$servers = $servers_map->getServersByProject($project->getId());

		foreach ($servers as $server)
		{
			/* @var $server GD_Model_Server */
			$servers_map->delete($server);
		}

		$projects = new GD_Model_ProjectsMapper();
		$projects->delete($project);
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

	public function getDomQuery($path)
	{
		$content = $this->response->outputBody();
		$domQuery = new Zend_Dom_Query($content);
		$result = $domQuery->query($path);
		return $result;
	}

	public function assertDomQuery($path, $message = '')
	{
		$result = $this->getDomQuery($path);

		$this->assertGreaterThan(0, count($result), $message);
	}
}
