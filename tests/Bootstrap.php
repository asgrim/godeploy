<?php

define('TESTUSER', 'testuser');
define('TESTPASSWORD', 'testpassword');

error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

$rootPath = realpath(dirname(__DIR__));

if(!defined('APPLICATION_PATH'))
{
	define('APPLICATION_PATH', $rootPath . '/application');
}
if(!defined('APPLICATION_ENV'))
{
	define('APPLICATION_ENV', 'development');
}

set_include_path(implode(PATH_SEPARATOR, array('.', $rootPath . '/library', get_include_path())));

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('GD_');
$loader->registerNamespace('MAL_');

require_once $rootPath . '/tests/application/ControllerTestCase.php';

$_SERVER['REQUEST_URI'] = '/';
