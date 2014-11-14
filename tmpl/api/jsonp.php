<?php

namespace Sacfeed;

header('Content-Type: application/javascript; charset=UTF-8');

echo 'sacfeed.callbacks["', $callback, '"](', json_encode([
	'status' => $status,
	'headers' => $headers,
	'result' => $result
], Config::DEVMODE ? JSON_PRETTY_PRINT : 0), ');';
