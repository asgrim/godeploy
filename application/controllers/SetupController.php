<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 James Titcumb, Simon Wade
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author James Titcumb, Jon Wigham, Simon Wade
 * @link http://www.godeploy.com/
 */
class SetupController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$this->view->show_navigation = false;
    }

    public function indexAction()
    {
    	$this->_redirect("/setup/step1");
    }

    public function step1Action()
    {

    }

    public function step2Action()
    {
		$this->view->headLink()->appendStylesheet("/css/template/form.css");

		$form = new GDApp_Form_SetupDatabase();
		$this->view->form = $form;

		if($this->getRequest()->isPost())
		{
			if($form->isValid($this->getRequest()->getParams()))
			{
				// Check the db connection works first...
				$db_connection_result = true;

				if($db_connection_result)
				{
					$setup_session = new Zend_Session_Namespace('gd_setup_session');
					$setup_session->database->host = $this->_getParam('hostname');
					$setup_session->database->username = $this->_getParam('db_username');
					$setup_session->database->password = $this->_getParam('db_password');
					$setup_session->database->dbname = $this->_getParam('dbname');

					$this->_redirect("/setup/step3");
				}
				else
				{
					$form->addError("DB_CONNECT_ERROR");
				}
			}
		}
    }

    public function step3Action()
    {
    	$this->view->headLink()->appendStylesheet("/css/template/form.css");

		$form = new GDApp_Form_SetupAdmin();
		$this->view->form = $form;

		if($this->getRequest()->isPost())
		{
			if($form->isValid($this->getRequest()->getParams()))
			{
				$setup_session = new Zend_Session_Namespace('gd_setup_session');
				$setup_session->admin->username = $this->_getParam('username');
				$setup_session->admin->password = $this->_getParam('password');

				$this->_redirect("/setup/dosetup");
			}
		}
    }

    public function dosetupAction()
    {
    	$_user_config_file = APPLICATION_PATH . '/configs/config.ini';

    	// Create the config ini from session
    	$setup_session = new Zend_Session_Namespace('gd_setup_session');

    	$config = new Zend_Config(array(), true);

    	$config->general = array();
    	$config->general->setupComplete = true;
    	$config->general->cryptkey = md5(microtime() . $setup_session->admin->username . $setup_session->admin->password);
    	$config->general->installDate = date("d/m/Y H:i:s");

    	$config->database = array();
    	$config->database->adapter = "PDO_MYSQL";
    	$config->database->host = $setup_session->database->host;
    	$config->database->username = $setup_session->database->username;
    	$config->database->password = $setup_session->database->password;
    	$config->database->dbname = $setup_session->database->dbname;

    	$writer_opts = array(
    		'config' => $config,
			'filename' => $_user_config_file,
    	);
    	$writer = new Zend_Config_Writer_Ini($writer_opts);
    	$writer->write();

    	// Load the database manually
		Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapter, $config->database->toArray()));
		Zend_Registry::set("cryptkey", $config->general->cryptkey);

    	// Run the appropriate database setup script
    	$db_adm = new GD_Db_Admin(
    		$config->database->host,
    		$config->database->username,
    		$config->database->password,
    		$config->database->dbname
    	);
    	$db_adm->installDatabase();

    	// Create the first user in the database
    	$userMapper = new GD_Model_UsersMapper();
    	$crypt = new GD_Crypt();

    	$user = new GD_Model_User();
    	$user->setName($setup_session->admin->username);
    	$user->setPassword($crypt->makeHash($setup_session->admin->password));
    	$userMapper->save($user);

    	$this->_redirect("/setup/complete");
    }

    public function completeAction()
    {
    	// TODO - dump what's in the config file basically
    	$this->_redirect("/");
    }
}

