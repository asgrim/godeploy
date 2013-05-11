<?php

class GD_Controller_Action_Helper_Api extends Zend_Controller_Action_Helper_Abstract
{
	private $Formats;

	public function direct()
	{
		$this->Formats = $this->getRequestResponseFormats();

		if (!is_array($this->Formats))
		{
			// 415 = Unsupported media format
			GD_Debug::Log("Content-type header or format URL parameter missing in API request", GD_Debug::DEBUG_BASIC);
			return $this->sendResponse(415, 'text');
		}
		else
		{
			list($req_format, $res_format) = $this->Formats;
		}

		if (!$this->checkApiEnabled())
		{
			GD_Debug::Log("API request attempted but URL trigger not enabled", GD_Debug::DEBUG_BASIC);
			return $this->sendResponse(503, $res_format);
		}

		// Attempt to parse the payload in the expected format.
		// If it fails, respond with 400 (bad request)
		try
		{
			$payload_raw = file_get_contents('php://input');
			$payload = $this->parsePayload($payload_raw, $req_format);
		}
		catch(Exception $e)
		{
			GD_Debug::Log("Failed to parse API payload. Exception thrown with message: " . $e->getMessage(), GD_Debug::DEBUG_BASIC);
			return $this->sendResponse(400, $res_format);
		}

		// Check the requested token matches what we have
		$our_token = GD_Config::get("url_trigger_token");

		if ($our_token != $payload['token'])
		{
			GD_Debug::Log("Token '{$payload['token']}' was not the correct token.", GD_Debug::DEBUG_BASIC);
			return $this->sendResponse(403, $res_format);
		}

		return $payload;
	}

	public function respond($http_status, $message = null)
	{
		if (!is_null($message))
		{
			GD_Debug::Log($message, GD_Debug::DEBUG_BASIC);
		}

		return $this->sendResponse($http_status, $this->Formats[1]);
	}

	private function getRequestResponseFormats()
	{
		// Try getting the request format from Content-type header, then URL ?format=xxx
		if (isset($_SERVER['CONTENT_TYPE']))
		{
			$req_format = $this->parseMime($_SERVER['CONTENT_TYPE']);
		}
		else if (isset($_GET['format']))
		{
			$req_format = $this->parseMime($_GET['format']);
		}
		else
		{
			return false;
		}

		// If they've set an Accept header, use it, otherwise respond in the
		// same format as the request
		if (isset($_SERVER['HTTP_ACCEPT']))
		{
			$res_format = $this->parseMime($_SERVER['HTTP_ACCEPT']);
		}
		else
		{
			$res_format = $req_format;
		}

		return array($req_format, $res_format);
	}

	private function parsePayload($request, $expected_format)
	{
		switch ($expected_format)
		{
			case 'json':
				return Zend_Json::decode($request, Zend_Json::TYPE_ARRAY);

			case 'xml':
				libxml_use_internal_errors(true);
				$xml = simplexml_load_string($request);
				if (!$xml)
				{
					// If we wanted we could use libxml_get_errors to get the errors...
					throw Exception("Not xml");
				}
				return array(
					'token' => (string)$xml->token,
					'server' => (int)$xml->server,
					'to' => (string)$xml->to,
					'comment' => (string)$xml->comment,
				);

			default:
				if ($request == '')
				{
					$request = $_SERVER['QUERY_STRING'];
				}
				parse_str($request, $output);
				return $output;
		}
	}

	private function checkApiEnabled()
	{
		return (GD_Config::get('enable_url_trigger') == '1');
	}

	private function sendResponse($http_status, $format)
	{
		$this->getActionController()->getHelper('ViewRenderer')->setNoRender();
		$this->getActionController()->getHelper('Layout')->disableLayout();

		$this->getResponse()->setHttpResponseCode($http_status);

		$result = ($http_status == 200 ? 'success' : 'failed');
		$data = array(
			"result" => $result,
		);

		switch($format)
		{
			case 'json':
				$this->getResponse()->setHeader('Content-type', 'application/json');
				$response = Zend_Json::encode($data);
				break;
			case 'xml':
				$this->getResponse()->setHeader('Content-type', 'text/xml');
				$xml = new SimpleXMLElement('<deployment/>');
				array_walk_recursive(array_flip($data), array($xml, 'addChild'));
				$response = $xml->asXML();
				break;
			default:
				$this->getResponse()->setHeader('Content-type', 'text/plain');
				$response = $result;
				break;
		}

		$this->getResponse()->appendBody($response . "\n");

		return ($result == 'success');
	}

	private function parseMime($mime)
	{
		if (strpos($mime, ';') !== false)
		{
			$mime = substr($mime, 0, strpos($mime, ';'));
		}

		switch (strtolower($mime))
		{
			case 'json':
			case 'application/json':
				return 'json';
			case 'xml':
			case 'text/xml':
				return 'xml';
			case 'text':
			case 'text/plain':
				return 'text';
			default:
				return null;
		}
	}
}