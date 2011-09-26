<?php

function _e($s) { echo GD_Translate::translate($s); }
function _r($s) { echo GD_Translate::translate($s); }

class GD_Translate
{
	/**
	 * @var Zend_Translate
	 */
	private static $_translate;

	public static function init($language)
	{
		$langfile = APPLICATION_PATH . '/../languages/' . $language . '.mo';

		if(!file_exists($langfile))
		{
			die("Language '{$language}' specified in config.ini was not a supported language.");
		}

		self::$_translate = new Zend_Translate(
			array(
				'adapter' => 'gettext',
				'content' => $langfile,
			)
		);
	}

	public static function translate($string)
	{
		return self::$_translate->_($string);
	}
}