<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 the authors listed in AUTHORS file
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
 * @author See AUTHORS file
 * @link http://www.godeploy.com/
 */
class SetupController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
		$this->view->headTitle('Setup');
		$this->view->show_navigation = false;
		$this->view->prenavigation_header = "setup/header.phtml";
		$this->view->available_languages = GD_Translate::getAvailableLanguages();

		$setup_session = new Zend_Session_Namespace('gd_setup_session');

		if(!isset($setup_session->language) || $setup_session->language == "")
		{
			$setup_session->language = "english";
		}

		$this->view->current_language = $setup_session->language;
	}

	public function indexAction()
	{
		$this->_redirect("/setup/step1");
	}

	public function languageAction()
	{
		if($this->getRequest()->isPost())
		{
			$setup_session = new Zend_Session_Namespace('gd_setup_session');
			$setup_session->language = $this->_getParam("lang");
		}

		if($this->_getParam("return"))
		{
			$url = $this->_getParam("return");
		}
		else
		{
			$url = "/setup/step1";
		}
		$this->_redirect($url);
	}

	public function step1Action()
	{
		$this->view->headTitle('Step 1');
		$this->view->headLink()->appendStylesheet("/css/template/table.css");

		$sh = new MAL_Util_Shell();
		$requirements = array();

		$requirements[_r("PHP version greater than 5.3.2")] = array(
			"ACTUAL" => PHP_VERSION,
			"RESULT" => PHP_VERSION_ID >= 50302,
		);

		$requirements[_r("PHP mcrypt module installed")] = array(
			"ACTUAL" => (extension_loaded("mcrypt") ? _r("OK") : _r("Not installed")),
			"RESULT" => extension_loaded("mcrypt"),
		);

		$requirements[_r("PHP ftp module installed")] = array(
			"ACTUAL" => (extension_loaded("ftp") ? _r("OK") : _r("Not installed")),
			"RESULT" => extension_loaded("ftp"),
		);

		$requirements[_r("PHP Safe mode is disabled")] = array(
			"ACTUAL" => ini_get('safe_mode') ? "safe_mode = " . ini_get('safe_mode') : _r("Not set"),
			"RESULT" => ini_get('safe_mode') != '1',
		);

		$requirements[_r("MySQL installed")] = array(
			"ACTUAL" => (extension_loaded("pdo_mysql") ? _r("OK") : _r("Not installed")),
			"RESULT" => extension_loaded("pdo_mysql"),
		);

		$sh->Exec("echo test");
		$requirements[_r("Permission to run 'exec' function")] = array(
			"ACTUAL" => (($sh->getLastOutput() == array("test")) ? _r("OK") : _r("Could not run")),
			"RESULT" => ($sh->getLastOutput() == array("test")),
		);

		$sh->Exec("ssh -v");
		$r = $sh->getLastOutput();
		$requirements[_r("OpenSSH installed")] = array(
			"ACTUAL" => $r[0],
			"RESULT" => strpos($r[0], "OpenSSH") !== false,
		);

		$sh->Exec("git --version");
		$r = $sh->getLastOutput();
		$requirements[_r("Git installed")] = array(
			"ACTUAL" => $r[0],
			"RESULT" => strpos($r[0], "git version ") !== false,
		);

		$requirements[_r("May not work with Suhosin")] = array(
			"ACTUAL" => $this->hasSuhosin() ? _r("Suhosin is enabled") . " - <strong>" . _r("GoDeploy may not function correctly") . "</strong>" : _r("Suhosin not enabled"),
			"RESULT" => !($this->hasSuhosin()),
			"NOT_CRITICAL" => true,
		);

		$requirements[_r("HOME directory environment variable is set")] = array(
			"ACTUAL" => getenv('HOME'),
			"RESULT" => getenv('HOME') != '',
		);

		$cfg_test = APPLICATION_PATH . "/configs/config.ini";
		$fh = @fopen($cfg_test, "a+");
		$requirements[_r("Config file writable")] = array(
			"ACTUAL" => $cfg_test . " " . _r("is") . " " . ($fh === false ? "<strong>" . _r("not writable") . "</strong>" : _r("writable")),
			"RESULT" => ($fh !== false),
			"NOT_CRITICAL" => true,
		);
		if($fh !== false)
		{
			fclose($fh);
			@unlink($cfg_test);
		}

		$cache_test = str_replace("/application", "/gitcache/.test", APPLICATION_PATH);
		$fh = @fopen($cache_test, "a+");
		$requirements[_r("gitcache directory writable")] = array(
			"ACTUAL" => $cache_test . " " . _r("is") . " " . ($fh === false ? "<strong>" . _r("not writable") . "</strong>" : _r("writable")),
			"RESULT" => ($fh !== false),
		);
		if($fh !== false)
		{
			fclose($fh);
			@unlink($cache_test);
		}

		// Check we've passed everything
		$passed = true;
		foreach($requirements as $rq)
		{
			if($rq["RESULT"] === false && !isset($rq["NOT_CRITICAL"]))
			{
				$passed = false;
			}
		}

		$this->view->passed = $passed;

		$this->view->requirements = $requirements;
	}

	public function step2Action()
	{
		$this->view->headTitle('Step 2');
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
		$this->view->headTitle('Step 3');
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
		$this->view->headTitle('Configuration');
		$_user_config_file = APPLICATION_PATH . '/configs/config.ini';

		// Create the config ini from session
		$setup_session = new Zend_Session_Namespace('gd_setup_session');

		if (!$setup_session->complete)
		{
			$config = new Zend_Config(array(), true);

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

			try
			{
				$writer->write();
			}
			catch(Exception $ex)
			{
				if(strpos($ex->getMessage(), 'Could not write to file') !== false)
				{
					$setup_session->ini_string = $writer->render();
				}
			}

			// Load the database manually
			Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapter, $config->database->toArray()));

			// Run the appropriate database setup script
			$db_adm = new GD_Db_Admin(
				$config->database->host,
				$config->database->username,
				$config->database->password,
				$config->database->dbname
			);
			$db_adm->installDatabase();

			// Set the other config values into database
			GD_Config::set("language", $setup_session->language ? $setup_session->language : "english");
			GD_Config::set("setup_complete", "1");
			GD_Config::set("cryptkey", md5(microtime() . $setup_session->admin->username . $setup_session->admin->password));
			GD_Config::set("install_date", date("d/m/Y H:i:s"));

			// Create the first user in the database
			$userMapper = new GD_Model_UsersMapper();
			$crypt = new GD_Crypt();

			$user = new GD_Model_User();
			$user->setName($setup_session->admin->username)
				->setPassword($crypt->makeHash($setup_session->admin->password))
				->setDateAdded(date('Y-m-d H:i:s'))
				->setAdmin(1)
				->enableUser();

			$userMapper->save($user);

			// Setup the SSH keypair
			$ssh_key = new GD_Model_SSHKey();
			$ssh_key->setSSHKeyTypesId(1);
			$ssh_key->generateKeyPair();
			//$ssh_key->setId(1);

			$ssh_keys_map = new GD_Model_SSHKeysMapper();
			$ssh_key_id = $ssh_keys_map->save($ssh_key);

			GD_Config::set("ssh_key_id", $ssh_key_id);

			$setup_session->complete = true;
		}

		if(isset($setup_session->ini_string))
		{
			$this->view->ini = $setup_session->ini_string;
		}
		else
		{
			$this->_redirect("/setup/complete");
		}
	}

	public function completeAction()
	{
		// TODO - dump what's in the config file basically
		$this->_redirect("/");
	}

	public function hasSuhosin()
	{
		// http://stackoverflow.com/questions/3383916/how-to-check-whether-suhosin-is-installed
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();
		if (strpos($phpinfo, "Suhosin") !== false)
			return true;

		return false;
	}
}

