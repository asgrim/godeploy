<?php

$payload_raw = array(
	"token" => "foobar",
	"to" => "latest",
	"comment" => "Comment lols!",
);

$type = "text/xml";
$type = "application/json";
//$type = "text/plain";

$xml = new SimpleXMLElement('<deployment/>');
array_walk_recursive(array_flip($payload_raw), array($xml, 'addChild'));
$payload = $xml->asXML();

$payload = json_encode($payload_raw);
//$payload = http_build_query($payload_raw);

$headers = array(
    "Accept: {$type}",
    "Content-type: {$type}",
    "Content-length: " . strlen($payload),
);

echo $payload . "\n\n";
print_r($headers); echo "\n\n--------------------------\n\n";

$url = "http://godeploy.localhost/project/agodeploy-test-project/deploy/api";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
