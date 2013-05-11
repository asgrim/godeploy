<?php

define('TEST_USER', 'testuser');
define('TEST_PASSWORD', 'testpassword');
//define('TEST_FTP_HOSTNAME', 'hostname');
//define('TEST_FTP_USERNAME', 'username');
//define('TEST_FTP_PASSWORD', 'password');
//define('TEST_FTP_PORT', 21);
//define('TEST_FTP_REMOTE_PATH', 'public_html/');
//define('GITHUB_USER', 'asgrim');

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

set_include_path(implode(PATH_SEPARATOR, array('.', $rootPath . '/', get_include_path())));

require 'vendor/autoload.php';

$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('GD_');
$loader->registerNamespace('MAL_');

require_once $rootPath . '/tests/application/ControllerTestCase.php';
require_once $rootPath . '/tests/library/TestCase.php';
