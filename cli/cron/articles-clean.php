#!/usr/bin/php
<?php

namespace Sacfeed;

use DateTime;
use DateInterval;
use MongoDB\BSON\UTCDateTime as MongoDateTime;

use Sacfeed\DB\Article;

require __DIR__ . '/../../sys/bootstrap.php';

CLI::init(__FILE__, 'Sacfeed -- clean old articles cli');


$dt = new DateTime();
$dt->sub(new DateInterval('P90D'));
$mdt = new MongoDateTime($dt);

Database::remove(Article::COLLECTION, ['ts' => ['$lt' => $mdt]], 0, true);

CLI::notice('Old articles (4 weeks) have been removed');
