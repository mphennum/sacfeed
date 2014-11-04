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
	echo '<article>';
	if ($article['thumb']) {
		echo '<img src="', $article['thumb'], '" alt="', $article['thumb'], '">';
	}

	$dt = new DateTime('@' . $article['ts'] / 1000, App::$utc);
	$dt->setTimezone($pst);

	if ($dt->getTimestamp() > $today) {
		$date = $dt->format('g:i a');
	} else {
		$date = $dt->format('l, F j');
	}

	echo
		'<h2>', $article['title'], '</h2>',
		'<p>', $article['summary'], '</p>',
		'<p>', $article['author'], '</p>',
		'<p>', $date, '</p>',
		'</article>', "\n"
	;
}
?>
</main>

<? include(__DIR__ . '/ssi/scripts.php'); ?>

</body>

</html>
