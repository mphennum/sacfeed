#!/usr/bin/php
<?php

namespace Sacfeed;

use MongoDate;

use Sacfeed\DB\Section;
use Sacfeed\DB\Article;

require __DIR__ . '/../../sys/bootstrap.php';

CLI::init('Sacfeed -- pull new articles cli');

$ts = new MongoDate();
$ts->sec -= 60 * 60 * 3;

$n = 0;
$seen = [];
$cursor = Section::find(['ts' => ['$gt' => $ts]], ['_id' => 1]);
foreach ($cursor as $record) {
	$section = $record['_id'];

	$url = 'http://' . Config::SACBEEHOST . $section . Config::JSONQUERY;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$json = curl_exec($ch);
	$info = curl_getinfo($ch);

	if ($json === false || trim($json) === '' || $info['http_code'] !== 200) {
		CLI::error('curl failed: ' . $url);
	}

	CLI::message($url);
	$json = json_decode($json, true);

	foreach ($json['items'] as $item) {
		$id = (int) $item['id'];

		if (isset($seen[$id])) {
			continue;
		}

		$seen[$id] = true;

		$article = new Article();
		if ($article->findOne($id)) {
			continue;
		}

		$article->setJSONFields($section, $item);
		$article->insert(1);

		++$n;
		CLI::message($article->title);
	}
}

$old = new MongoDate();
$old->sec -= 60 * 60 * 24 * 7;
Database::remove(Article::COLLECTION, ['ts' => ['$lt' => $old]], 0, true);

CLI::notice('Old articles (7 days) have been removed');
CLI::notice($n . ' articles inserted');
