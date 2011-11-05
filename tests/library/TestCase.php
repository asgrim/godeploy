<?php

class GD_TestCase extends PHPUnit_Framework_TestCase
{
	public function loadDatabaseFromConfig()
	{
		$db_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'database');
		$adapter = Zend_Db::factory($db_conf->adapter, $db_conf->toArray());
		Zend_Db_Table::setDefaultAdapter($adapter);
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
}