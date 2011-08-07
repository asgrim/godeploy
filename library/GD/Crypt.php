<?php

class GD_Crypt extends MAL_Crypt
{
	private $_key;

	public function __construct()
	{
		$raw_cryptkey = Zend_Registry::get("db")->cryptkey;
		if(!isset($raw_cryptkey) || $raw_cryptkey == "")
		{
			throw new GD_Exception("The 'cryptkey' value must be specified in db.ini - see db.ini.example for example.");
		}
		$this->_key = md5($raw_cryptkey);
	}

	public function setKey($key)
	{
		$this->_key = $key;
	}

	public function doEncrypt($data)
	{
		return parent::Encrypt($data, $this->_key);
	}

	public function doDecrypt($data)
	{
		return parent::Decrypt($data, $this->_key);
	}
}