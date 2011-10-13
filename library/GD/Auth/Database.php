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
 * @author James Titcumb, Simon Wade
 * @link http://www.godeploy.com/
 */

/**
 * Authenticate against the configured database
 * @author james
 */
class GD_Auth_Database implements Zend_Auth_Adapter_Interface
{
	/**
	 * Username to log in witih
	 * @var string
	 */
	protected $_username;

	/**
	 * Password to log in with
	 * @var string
	 */
	protected $_password;

	/**
	 * The currently logged in user object - used to cache for
	 * GD_Auth_Database::GetLoggedInUser()
	 * @var GD_Model_User
	 */
	private static $_currentUser;

	/**
	 * Constructor for GD_Auth_Database, simply sets username and password
	 *
	 * @param $username string The username provided by the user in the login form
	 * @param $password string The password provided by the user in the login form
	 * @return GD_Auth_Database
	 */
	public function __construct($username,$password)
	{
		$this->_username = $username;
		$this->_password = $password;
	}

	/**
	 * Authentication function to attempt to login the user using the credentials supplied in the constructor.
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		// Get user details for the email we're trying to get
		$users = new GD_Model_UsersMapper();

		$user = $users->getUserByName($this->_username, true);

		if(is_null($user))
		{
			GD_Debug::Log("Authentication failure - user '{$this->_username}' not found.", GD_Debug::DEBUG_BASIC);
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_username);
		}

		$stored_fullhash = $user->getPassword();
		$salt_len = strrpos($stored_fullhash, "$") + 1;
		$salt = substr($stored_fullhash, 0, $salt_len);
		$stored_hash = substr($stored_fullhash, $salt_len, strlen($stored_fullhash) - $salt_len);

		$compare_hash = crypt($this->_password, $salt);

		// If we passed the tests, then we successfully authenticated
		if($this->_username == $user->getName() && $compare_hash == $user->getPassword())
		{
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$this->_username);
		}
		else
		{
			GD_Debug::Log("Authentication failure - incorrect password for '{$this->_username}'.", GD_Debug::DEBUG_BASIC);
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,$this->_username);
		}
	}

	/**
	 * Handy dandy function to get the GD_Model_User object from the currently
	 * logged in Zend_Auth identity. Returns null on failure.
	 *
	 * @return GD_Model_User|null
	 */
	public static function GetLoggedInUser()
	{
		if(!isset(self::$_currentUser) || is_null(self::$_currentUser) || !(self::$_currentUser instanceof GD_Model_User))
		{
			$auth = Zend_Auth::getInstance();
			$username = $auth->getIdentity();

			if(is_null($username))
			{
				return null;
			}

			$users = new GD_Model_UsersMapper();
			self::$_currentUser = $users->getUserByName($username, true);

			return self::$_currentUser;
		}
		else return self::$_currentUser;
	}
}