<?php

// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure the root is on include_path
set_include_path(
	implode(
		PATH_SEPARATOR, array(
			realpath(APPLICATION_PATH . '/../'),
			get_include_path(),
		)
	)
);

require 'vendor/autoload.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/system.ini'
);
$application->bootstrap()
			->run();