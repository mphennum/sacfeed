#!/usr/bin/php
<?php

namespace Sacfeed;

use Sacfeed\DB\Section;

require __DIR__ . '/../../sys/bootstrap.php';

$opts = getopt('v', ['help']);

if (isset($opts['help'])) {
	CLI::message('Sacfeed -- update sections cli');
	CLI::message('usage: ', 'sections-update.php [OPTIONS]');
	CLI::message('-v     ', 'verbose');
	exit(0);
}

if (isset($opts['v'])) {
	CLI::$verbose = true;
}

CLI::title('Sacfeed -- update sections cli');

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

$seen = [];
$sections = [];
for ($i = 0, $n = count($urls); $i < $n; ++$i) {
	$url = &$urls[$i];
	if (!preg_match('/^(?:https?:)?\/\/' . str_replace('.', '\\.', Config::SACBEEHOST) . '(.*)$/', $url, $m)) {
		CLI::warning('BAD URL - ' . $url);
		continue;
	}

	$slug = &$m[1];
	$slug = strtolower($slug);
	$slug = trim($slug, '/');
	$slug = ($slug === '') ? '/' : '/' . $slug . '/';

	if (isset($seen[$slug])) {
		CLI::message('DUPLICATE SLUG - ' . $slug);
		continue;
	}

	if (isset($skip[$slug])) {
		CLI::message('SKIPPING SLUG - ' . $slug);
		continue;
	}

	$seen[$slug] = true;

	$section = new Section();
	$section->genUUID();
	$section->name = $names[$i];
	$section->slug = $slug;

	CLI::message($section->name . ': ', $section->slug);

	$sections[] = $section->getFields();
}

Database::remove(Section::COLLECTION, [], 1, true);
Database::batchInsert(Section::COLLECTION, $sections);
