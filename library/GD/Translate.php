<?php

function _e($s) { echo GD_Translate::translate($s); }
function _r($s) { return GD_Translate::translate($s); }

class GD_Translate
{
	/**
	 * @var Zend_Translate
	 */
	private static $_translate;

	private static $_languages_path;

	public static function init($language)
	{
		$langfile = self::getLanguagePath() . $language . '.mo';

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

	public static function getAvailableLanguages()
	{
		$path = self::getLanguagePath();

		$langs = array();

		if($path)
		{
			$dir = new DirectoryIterator($path);
			foreach ($dir as $fileInfo)
			{
				$fn = $fileInfo->getFilename();
				if(!$fileInfo->isDot() && substr($fn, strlen($fn)-3, 3) == ".mo")
				{
					$p = strrchr($fn, '.');

					if($p !== false)
					{
						$fn = substr($fn, 0, -strlen($p));
					}

					$langs[] = $fn;
				}
			}
		}
		return $langs;
	}

	private static function getLanguagePath()
	{
		if(!isset(self::$_languages_path))
		{
			self::setLanguagePath();
		}
		return self::$_languages_path;
	}

	private static function setLanguagePath($path = null)
	{
		if(!is_null($path))
		{
			self::$_languages_path = $path;
		}
		else
		{
			self::$_languages_path = APPLICATION_PATH . '/../languages/';
		}
	}
}
