<?php

namespace Sacfeed;

require __DIR__ . '/../conf/Config.php';
require __DIR__ . '/../lib/App.php';
require __DIR__ . '/../lib/Cache.php';
require __DIR__ . '/../lib/Database.php';
require __DIR__ . '/../lib/Request.php';
require __DIR__ . '/../lib/Response.php';

require __DIR__ . '/../lib/db/Record.php';
require __DIR__ . '/../lib/db/Article.php';
require __DIR__ . '/../lib/db/Author.php';
require __DIR__ . '/../lib/db/Section.php';

if (PHP_SAPI === 'cli') {
	require __DIR__ . '/../lib/CLI.php';
}

App::init();
