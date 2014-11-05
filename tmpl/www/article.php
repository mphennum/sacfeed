<?php

namespace Sacfeed;

use DateTime;
use DateTimeZone;

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="en">

<head>

<? include(__DIR__ . '/ssi/meta.php'); ?>

<title>sacbee article feed - sacfeed</title>

<? include(__DIR__ . '/ssi/styles.php'); ?>

</head>

<body>

<header>

<h1><a href="http://<?= Config::WWWHOST ?>/">sacfeed</a></h1>

<button class="sf-navbtn"><hr><hr><hr></button>

<nav>
<?
foreach ($response['sections'] as $section) {
	echo '<a href="http://', Config::WWWHOST, $section['id'], '">', $section['name'], '</a>', "\n";
}
?>
</nav>

</header>

<main>
<?
$pst = new DateTimeZone('America/Los_Angeles');
$today = new DateTime('today', $pst);
$today = $today->getTimestamp();

foreach ($response['articles'] as $article) {
	echo
		'<article>', "\n",
		'<div class="sf-top">', "\n"
	;

	if ($article['thumb']) {
		echo '<p><a href="', $article['url'], '"><img src="', $article['thumb'], '" alt="', $article['thumb'], '"></a></p>', "\n";
	}

	$dt = new DateTime('@' . ($article['ts'] / 1000), App::$utc);
	$dt->setTimezone($pst);

	if ($dt->getTimestamp() > $today) {
		$date = $dt->format('g:i A');
	} else {
		$date = $dt->format('l, F j');
	}

	$author = preg_replace('/^By\s+/', '', $article['author']);
	if (preg_match('/\s+([^@\s]+@[^@\s]+)$/', $author, $m)) {
		$author = '<span class="sf-name">' . preg_replace('/\s+[^@\s]+@[^@\s]+$/', '', $author) . '</span> ' . $m[1];
	} else {
		$author = '<span class="sf-name">' . preg_replace('/^the\s/', 'The ', $author) . '</span>';
	}

	echo
		'<h2><a href="', $article['url'], '">', $article['title'], '</a></h2>', "\n",
		'<p class="sf-summary">', $article['summary'], '</p>', "\n",
		'</div>', "\n",
		'<div class="sf-bottom">', "\n",
		'<p>', $author, '</p>', "\n",
		'<p>', $date, '</p>', "\n",
		'</div>', "\n",
		'</article>', "\n"
	;
}
?>
</main>

<? include(__DIR__ . '/ssi/scripts.php'); ?>

</body>

</html>
