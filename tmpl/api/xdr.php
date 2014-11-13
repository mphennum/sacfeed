<?php

namespace Sacfeed;

header('Content-Type: application/json; charset=UTF-8');

$response = [
	'status' => $status,
	'headers' => $headers,
	'result' => $result
];

echo json_encode($response, Config::DEVMODE ? JSON_PRETTY_PRINT : 0);
