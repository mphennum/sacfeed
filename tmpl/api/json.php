<?php

namespace Sacfeed;

header('Content-Type: application/json; charset=UTF-8');

if ($response !== null) {
	echo empty($response) ? '{}' : json_encode($response, Config::DEVMODE ? JSON_PRETTY_PRINT : 0);
}

echo "\n";
