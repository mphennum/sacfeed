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
	// date
	$dt = new DateTime('@' . ($article['ts'] / 1000), App::$utc);
	$dt->setTimezone($pst);

	if ($dt->getTimestamp() > $today) {
		$date = $dt->format('g:i A');
	} else {
		$date = $dt->format('l, F j');
	}

	// thumb
	if ($article['thumb']) {
		$thumb = '<p class="sf-thumb"><a href="' . $article['url'] . '"><img src="' . $article['thumb'] . '" alt="' . str_replace('"', '\'', $article['title']) . '"></a></p>' . "\n";
	} else {
		$thumb = '';
	}

	// author / profile

	$profile = '';
	$author = preg_replace('/^By\s+/', '', $article['author']);
	$author = trim($author);

	if ($author === '' && preg_match('/Dan\s+(Walters|Morain)/i', $article['title'], $m)) {
		$author = 'Dan ' . $m[1] . ' d' . strtolower($m[1]) . '@sacbee.com';
	}

	if (preg_match('/\s+([^@\s]+@[^@\s]+)$/', $author, $m)) {
		$author = preg_replace('/\s+[^@\s]+@[^@\s]+$/', '', $author);
		$authorLC = strtolower($author);
		if (isset($authorMap[$authorLC])) {
			$file = 'http://' . Config::IMGHOST . Config::AUTHORDIR . str_replace(' ', '-', $authorLC) . '.jpg';
			$profile = '<img class="sf-profile" src="' . $file . '" alt="' . $author . '">';
		} else if (preg_match('/^([^,]+),/', $author, $first) && isset($authorMap[strtolower($first[1])])) {
			$file = 'http://' . Config::IMGHOST . Config::AUTHORDIR . str_replace(' ', '-', strtolower($first[1])) . '.jpg';
			$profile = '<img class="sf-profile" src="' . $file . '" alt="' . $author . '">';
		}

		$author = '<p class="sf-byline"><span class="sf-name">' . $author . '</span> ' . $m[1] . '</p>';
	} else if ($author !== '') {
		$author = '<p class="sf-byline"><span class="sf-name">' . preg_replace('/^the\s/', 'The ', $author) . '</span></p>';
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
		$author, "\n",
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
