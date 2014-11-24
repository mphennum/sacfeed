#!/usr/bin/php
<?php

namespace Sacfeed;

use MongoDate;

use Sacfeed\DB\Article;

require __DIR__ . '/../../sys/bootstrap.php';

CLI::init(__FILE__, 'Sacfeed -- clean old articles cli');

$old = new MongoDate();
$old->sec -= 60 * 60 * 24 * 7 * 4;

Database::remove(Article::COLLECTION, ['ts' => ['$lt' => $old]], 0, true);

CLI::notice('Old articles (7 days) have been removed');
