#!/usr/bin/php
<?php

namespace Sacfeed;

use Sacfeed\DB\Article;

require __DIR__ . '/../../sys/local-bootstrap.php';

CLI::init(__FILE__, 'Sacfeed -- pull new articles from random section locally and send to api end point');

usleep(mt_rand(1000000 * 60 * 5, 1000000 * 60 * 60)); // 5m to 1h

// get list of all available sections

$url = 'https://' . Config::APIHOST . '/v0/section/list';
$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 5,
	CURLOPT_HTTPHEADER => [
		'X-Api-Key: ' . Config::APIKEY
	],
]);

$json = curl_exec($ch);
$info = curl_getinfo($ch);

//var_dump($json);
//CLI::printr($info);

if ($json === false || trim($json) === '' || $info['http_code'] !== 200) {
	CLI::error('curl failed: ' . $url);
}

$json = json_decode($json, true);
// CLI::printr($json);
$sections = $json['sections'] ?? null;
if ($sections === null) {
	CLI::error('empty sections in json resp from sacfeed api');
}

// pick random section

$section = $sections[mt_rand(0, count($sections) - 1)];
$section = [
	'id' => $section['id'],
	'name' => $section['name'],
	'ts' => $section['ts'],
];

// curl json feed

$url = 'https://' . Config::SACBEEHOST . $section['id'] . Config::JSONQUERY;

$ch = curl_init();
curl_setopt_array($ch, [
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_USERAGENT => Config::CURLUSERAGENT,
	CURLOPT_TIMEOUT => 5,
	CURLOPT_ENCODING => 'gzip',
	CURLOPT_HTTPHEADER => Config::$curlHeaders,
]);

$json = curl_exec($ch);
$info = curl_getinfo($ch);

if ($json === false || trim($json) === '' || $info['http_code'] !== 200) {
	CLI::error('curl failed: ' . $url);
}

CLI::notice($url);
$json = json_decode($json, true);

$seen = [ ];
$items = $json['items'];
foreach ($items as $item) {
	$id = (int) $item['id'];

	if (isset($seen[$id])) {
		continue;
	}

	$seen[$id] = true;

	CLI::message('[' . $id . '] ', $item['title']);

	// curl thumb

	$item['thumb'] = Article::getThumbFromAssets($item);

	// save article via post req

	$url = 'https://' . Config::APIHOST . '/v0/article/save';
	$ch = curl_init();
	curl_setopt_array($ch, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 5,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => json_encode([
			'section' => $section['id'],
			'article' => $item,
		]),
		CURLOPT_HTTPHEADER => [
			'X-Api-Key: ' . Config::APIKEY
		],
	]);

	$json = curl_exec($ch);
	$info = curl_getinfo($ch);

	if ($json === false || trim($json) === '' || $info['http_code'] > 299 || $info['http_code'] < 200) {
		CLI::error('curl failed: ' . $url);
	}

	$json = json_decode($json, true);
	// CLI::printr($json);
	// CLI::input('press any key to continue');

	usleep(mt_rand(1000000, 1000000 * 5)); // 1s to 5s
}
