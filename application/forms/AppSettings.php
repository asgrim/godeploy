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
class GDApp_Form_AppSettings extends GD_Form_Abstract
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$language = new Zend_Form_Element_Select('language');
		$language->setLabel(_r('Language'))
			->setRequired(true)
			->addValidator('NotEmpty');

		$langs = GD_Translate::getAvailableLanguages();
		foreach ($langs as $lang)
		{
			$language->addMultiOption($lang, ucwords($lang) . " (" . _r($lang) . ")");
		}

		$require_comments = new Zend_Form_Element_Select('require_comments');
		$require_comments->setLabel(_r('Require deployment comments'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$require_comments->addMultiOption(0, "No");
		$require_comments->addMultiOption(1, "Yes");

		$autofill_comments = new Zend_Form_Element_Select('autofill_comments');
		$autofill_comments->setLabel(_r('Attempt comment autofill'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$autofill_comments->addMultiOption(0, "No");
		$autofill_comments->addMultiOption(1, "Yes");

		$autofill_comments = new Zend_Form_Element_Select('force_preview');
		$autofill_comments->setLabel(_r('Force preview when deploying'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$autofill_comments->addMultiOption(0, "No");
		$autofill_comments->addMultiOption(1, "Yes");

		$enable_url_trigger = new Zend_Form_Element_Select('enable_url_trigger');
		$enable_url_trigger->setLabel(_r('Enable URL trigger'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$enable_url_trigger->addMultiOption(0, "No");
		$enable_url_trigger->addMultiOption(1, "Yes");

		$url_trigger_token = new Zend_Form_Element_Text('url_trigger_token');
		$url_trigger_token->setLabel(_r('URL trigger token'));

		$rows_per_history_page = new Zend_Form_Element_Select('rows_per_history_page');
		$rows_per_history_page->setLabel(_r('Rows per history page'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$rows_per_history_page->addMultiOption(10, "10");
		$rows_per_history_page->addMultiOption(15, "15");
		$rows_per_history_page->addMultiOption(20, "20");
		$rows_per_history_page->addMultiOption(25, "25");
		$rows_per_history_page->addMultiOption(30, "30");
		$rows_per_history_page->addMultiOption(35, "35");
		$rows_per_history_page->addMultiOption(40, "40");
		$rows_per_history_page->addMultiOption(45, "45");
		$rows_per_history_page->addMultiOption(50, "50");
		$rows_per_history_page->addMultiOption(60, "60");
		$rows_per_history_page->addMultiOption(70, "70");
		$rows_per_history_page->addMultiOption(80, "80");
		$rows_per_history_page->addMultiOption(90, "90");
		$rows_per_history_page->addMultiOption(100, "100");

		$enable_usage_stats = new Zend_Form_Element_Select('enable_usage_stats');
		$enable_usage_stats->setLabel(_r('Enable usage stats'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$enable_usage_stats->addMultiOption(0, "No");
		$enable_usage_stats->addMultiOption(1, "Yes");

		$debug_level = new Zend_Form_Element_Select('debug_level');
		$debug_level->setLabel(_r('Debug Level'))
			->setRequired(true)
			->addValidator('NotEmpty');
		$debug_level->addMultiOption(0, "Off");
		$debug_level->addMultiOption(1, "Basic");
		$debug_level->addMultiOption(2, "Full");

		$logfile = new Zend_Form_Element_Text('logfile');
		$logfile->setLabel(_r('Logfile Path'))
			->setRequired(true)
			->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Image('btn_submit');
		$submit->setImage('/images/buttons/small/save-changes.png');

		$this->addElements(
			array(
				$language,
				$require_comments,
				$autofill_comments,
				$enable_url_trigger,
				$url_trigger_token,
				$rows_per_history_page,
				$enable_usage_stats,
				$debug_level,
				$logfile,
				$submit,
			)
		);
	}
}