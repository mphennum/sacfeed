#!/usr/bin/php
<?php

namespace Sacfeed;

use MongoDate;

use Sacfeed\DB\Section;
use Sacfeed\DB\Article;

require __DIR__ . '/../../sys/bootstrap.php';

CLI::init(__FILE__, 'Sacfeed -- pull new articles cli');

$nn = 0; // number of new articles
$no = 0; // number of old articles
$seen = [];
$cursor = Section::find([], ['_id' => 1]);
foreach ($cursor as $record) {
	$section = $record['_id'];

	CLI::subtitle($section);

	$url = 'http://' . Config::SACBEEHOST . $section . Config::JSONQUERY;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$json = curl_exec($ch);
	$info = curl_getinfo($ch);

	if ($json === false || trim($json) === '' || $info['http_code'] !== 200) {
		CLI::error('curl failed: ' . $url);
	}

	CLI::notice($url);
	$json = json_decode($json, true);

	foreach ($json['items'] as $item) {
		$id = (int) $item['id'];

		if (isset($seen[$id])) {
			continue;
		}

		$seen[$id] = true;

		$article = new Article();
		$exists = $article->findOne($id);
		$article->setJSONFields($section, $item);

		if ($exists) {
			$article->update(1);
			++$no;
		} else {
			$article->insert(1);
			++$nn;
		}

		CLI::message('[' . $id . '] ', $article->title);

		sleep(1);
	}

	sleep(1);
}

CLI::notice($no . ' articles updated');
CLI::notice($nn . ' articles inserted');
