<?php

	class GD_Usage
	{
		const T_INSTALL = 'install';
		const T_UPGRADE = 'upgrade';

		private static function send($type, $json_packet)
		{
			if(GD_Config::get("enable_usage_stats") != '1') return;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://www.godeploy.com/usage.php?type={$type}");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_packet);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_USERAGENT, "GoDeploy usage statistics");

			$result = curl_exec($ch);

			if (curl_errno($ch) == 0)
			{
				$info = curl_getinfo($ch);

				if ($info["http_code"] == "200")
				{
					return true;
				}
			}
			return false;
		}

		private static function getAnonymousData()
		{
			$data = new stdClass();
			$data->install_id = GD_Config::get("unique_install_id");
			$data->install_date = GD_Config::get("install_date");
			$data->language = GD_Config::get("language");
			$data->db_version = GD_Config::get("db_version");
			$data->sw_version = GD_Config::get("sw_version");

			return $data;
		}

		public static function logInstall()
		{
			if(GD_Config::get("enable_usage_stats") != '1') return;

			self::Send(self::T_INSTALL, json_encode(self::getAnonymousData()));
		}

		public static function logUpgrade()
		{
			if(GD_Config::get("enable_usage_stats") != '1') return;

			self::Send(self::T_UPGRADE, json_encode(self::getAnonymousData()));
		}
	}