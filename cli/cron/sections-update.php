#!/usr/bin/php
<?php

namespace Sacfeed;

use MongoDate;

use Sacfeed\DB\Section;

require __DIR__ . '/../../sys/bootstrap.php';

CLI::init('Sacfeed -- update sections cli');

$url = 'http://www.sacbee.com/';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
$info = curl_getinfo($ch);

if ($html === false || trim($html) === '') {
	CLI::error('curl failed: ' . $url);
}

if (!preg_match_all('/<a(?:\s+href="([^"]*)")(?:[^>]*)?\s+id="menuPane-link-[0-9]+">\s*([a-z]+)\s*<b/i', $html, $matches)) {
	CLI::error('No sections were found');
}

$urls = $matches[1];
$names = $matches[2];
$n = count($urls);

if (empty($urls)) {
	CLI::error('No section urls could be found');
}

$skip = ['/classifieds/' => true];

$ts = new MongoDate();

$seen = [];
$sections = [];
for ($i = 0, $n = count($urls); $i < $n; ++$i) {
	$url = $urls[$i];
	if (!preg_match('/^(?:https?:)?\/\/' . str_replace('.', '\\.', Config::SACBEEHOST) . '(.*)$/', $url, $m)) {
		CLI::warning('BAD URL - ' . $url);
		continue;
	}

	$id = $m[1];
	$id = strtolower($id);
	$id = trim($id, '/');
	$id = ($id === '') ? '/' : '/' . $id . '/';

	if (isset($seen[$id])) {
		CLI::message('DUPLICATE SLUG - ' . $id);
		continue;
	}

	if (isset($skip[$id])) {
		CLI::message('SKIPPING SLUG - ' . $id);
		continue;
	}

	$seen[$id] = true;

	$section = new Section();
	$section->_id = $id;
	$section->name = $names[$i];
	$section->ts = $ts;

	CLI::message($section->_id . ': ', $section->name);

	$sections[] = $section->getFields();
}

Database::remove(Section::COLLECTION, [], 1, true);
Database::batchInsert(Section::COLLECTION, $sections);
