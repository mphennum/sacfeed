#!/usr/bin/php
<?php

namespace Sacfeed;

require __DIR__ . '/../../sys/bootstrap.php';

$opts = getopt('', ['help']);

if (isset($opts['help'])) {
	CLI::message('Sacfeed -- update sections cli');
	CLI::message('usage: ', 'sections-update.php [OPTIONS]');
	exit(0);
}

CLI::title('Sacfeed -- update sections cli');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.sacbee.com/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
$info = curl_getinfo($ch);

if ($html === false || trim($html) === '') {
	CLI::error('curl failed');
}

CLI::message($html);
