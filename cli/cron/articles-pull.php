#!/usr/bin/php
<?php

namespace Sacfeed;

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

	$url = 'https://' . Config::SACBEEHOST . $section . Config::JSONQUERY;
	// $url = 'https://www.sacbee.com/?widgetName=rssfeed&widgetContentId=339621&getJsonFeed=true&service=json';
	$ch = curl_init();
	curl_setopt_array($ch, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		// CURLOPT_USERAGENT => 'curl/7.29.0',
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
		CURLOPT_TIMEOUT => 15,
		CURLOPT_ENCODING => 'gzip',
		CURLOPT_HTTPHEADER => [
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			// 'Accept-encoding: gzip, deflate, br',
			'Accept-Language: en-US,en;q=0.9',
			'Cache-Control: no-cache',
			'DNT: 1',
			'Pragma: no-cache',
			'Upgrade-Insecure-Requests: 1',
		],
	]);

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

		usleep(mt_rand(1000000, 5000000));
	}

	usleep(mt_rand(1000000, 5000000));
}

CLI::notice($no . ' articles updated');
CLI::notice($nn . ' articles inserted');
