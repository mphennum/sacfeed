#!/usr/bin/php
<?php

namespace Sacfeed;

use MongoDate;

use Sacfeed\DB\Section;
use Sacfeed\DB\Article;

require __DIR__ . '/../../sys/bootstrap.php';

CLI::init('Sacfeed -- pull new articles cli');

$ts = new MongoDate();
$ts->sec -= 3600;
$cursor = Section::find(['ts' => ['$gt' => $ts]]);
