<?php

class GD_TestCase extends PHPUnit_Framework_TestCase
{
	public function loadDatabaseFromConfig()
	{
		$db_conf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', 'database');
		$adapter = Zend_Db::factory($db_conf->adapter, $db_conf->toArray());
		Zend_Db_Table::setDefaultAdapter($adapter);
	}
}