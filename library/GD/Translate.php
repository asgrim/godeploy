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

/**
 * Echo a translation directly of $s string
 * @param string $s
 */
function _e($s) { echo GD_Translate::translate($s); }

/**
 * Return a translation of $s string
 * @param string $s
 * @return string Translated text
 */
function _r($s) { return GD_Translate::translate($s); }

/**
 * An easy-peasy class to handle translations using Zend_Translate
 *
 * Call GD_Translate::init("english") to initialise the language, and also
 * declare the above _e and _r functions
 *
 * @author james
 *
 */
class GD_Translate
{
	/**
	 * @var Zend_Translate
	 */
	private static $_translate;

	/**
	 * @var string
	 */
	private static $_languages_path;

	/**
	 * Initialise the Zend_Translate object
	 *
	 * @param string $language Name of the language to use
	 */
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

	/**
	 * Use Zend_Translate object to translate the string specified
	 *
	 * @param string $string
	 * @return string The translated string
	 */
	public static function translate($string)
	{
		return self::$_translate->_($string);
	}

	/**
	 * Get a list of the available languages as an array of strings
	 *
	 * @return array
	 */
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
		sort($langs);
		return $langs;
	}

	/**
	 * Get the current language path
	 */
	private static function getLanguagePath()
	{
		if(!isset(self::$_languages_path))
		{
			self::setLanguagePath();
		}
		return self::$_languages_path;
	}

	/**
	 * Set the current language path from the parameter, or set to default if
	 * not specified
	 *
	 * @param string|null $path
	 */
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
