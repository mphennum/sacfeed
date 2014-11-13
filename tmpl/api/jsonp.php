<?php

namespace Sacfeed;

header('Content-Type: application/javascript; charset=UTF-8');

echo 'sacfeed.callbacks["', $callback, '"](',
	json_encode($status, Config::DEVMODE ? JSON_PRETTY_PRINT : 0), ',',
	json_encode($headers, Config::DEVMODE ? JSON_PRETTY_PRINT : 0), ',',
	json_encode($result, Config::DEVMODE ? JSON_PRETTY_PRINT : 0),
');'
;
