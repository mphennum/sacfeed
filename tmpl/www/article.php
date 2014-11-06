<?php

namespace Sacfeed;

use DateTime;
use DateTimeZone;

use Sacfeed\DB\Author;

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

$authorMap = $response['authorMap'];

$pst = new DateTimeZone('America/Los_Angeles');
$today = new DateTime('today', $pst);
$today = $today->getTimestamp();

foreach ($response['articles'] as $article) {
	$dt = new DateTime('@' . ($article['ts'] / 1000), App::$utc);
	$dt->setTimezone($pst);

	if ($dt->getTimestamp() > $today) {
		$date = $dt->format('g:i A');
	} else {
		$date = $dt->format('l, F j');
	}

	if ($article['thumb']) {
		$thumb = '<p class="sf-thumb"><a href="' . $article['url'] . '"><img src="' . $article['thumb'] . '" alt="' . str_replace('"', '\'', $article['title']) . '"></a></p>' . "\n";
	} else {
		$thumb = '';
	}

	$profile = '';
	$author = preg_replace('/^By\s+/', '', $article['author']);
	if (preg_match('/\s+([^@\s]+@[^@\s]+)$/', $author, $m)) {
		$author = preg_replace('/\s+[^@\s]+@[^@\s]+$/', '', $author);
		$authorLC = strtolower($author);
		if (isset($authorMap[$authorLC])) {
			$file = 'http://' . Config::IMGHOST . Config::AUTHORDIR . str_replace(' ', '-', $authorLC) . '.jpg';
			$profile = '<img class="sf-profile" src="' . $file . '" alt="' . $author . '">';
		}

		$author = '<span class="sf-name">' . $author . '</span> ' . $m[1];
	} else {
		$author = '<span class="sf-name">' . preg_replace('/^the\s/', 'The ', $author) . '</span>';
	}

	echo
		'<article>', "\n",
		'<div class="sf-top">', "\n",
		$thumb
	;

	echo
		'<h2><a href="', $article['url'], '">', $article['title'], '</a></h2>', "\n",
		'<p class="sf-summary">', $article['summary'], '</p>', "\n",
		'<p><a href="', $article['url'], '">read more</a></p>', "\n",
		'</div>', "\n",
		'<div class="sf-bottom">', "\n",
		$profile,
		'<p>', $author, '</p>', "\n",
		'<p class="sf-date">', $date, '</p>', "\n",
		'</div>', "\n",
		'</article>', "\n\n"
	;
}
?>
</main>

<? include(__DIR__ . '/ssi/scripts.php'); ?>

</body>

</html>
