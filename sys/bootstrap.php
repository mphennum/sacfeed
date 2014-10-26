<?php

namespace Sacfeed;

require __DIR__ . '/../conf/Config.php';
require __DIR__ . '/../lib/App.php';
require __DIR__ . '/../lib/Cache.php';
require __DIR__ . '/../lib/Database.php';
require __DIR__ . '/../lib/Request.php';
require __DIR__ . '/../lib/Response.php';

if (PHP_SAPI === 'cli') {
	require __DIR__ . '/../lib/CLI.php';
}

App::init();
