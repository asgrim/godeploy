<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('GD_');
	}

	protected function _initNavigation()
	{
		$navMode = 'navAccount'; // navLogin or navAccount

		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', $navMode);

		$navigation = new Zend_Navigation($config);
		$view->navigation($navigation);
	}
}

