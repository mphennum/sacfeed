<?php

namespace Sacfeed;

header('Content-Type: application/json; charset=UTF-8');

echo empty($response) ? '{}' : json_encode($response, Config::DEVMODE ? JSON_PRETTY_PRINT : 0);
