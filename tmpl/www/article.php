<?php

namespace Sacfeed;

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="en">

<head>

<? include(__DIR__ . '/ssi/meta.php'); ?>

<title>live sacbee.com article feed - sacfeed</title>

<? include(__DIR__ . '/ssi/styles.php'); ?>

</head>

<body>

<header><a href="http://<?= Config::WWWHOST ?>/">sacfeed</a></header>

<nav>
<?
foreach ($response['sections'] as $section) {
	echo '<a href="http://', Config::WWWHOST, $section['id'], '">', $section['name'], '</a>';
}
?>
</nav>

<main>
<?
foreach ($response['articles'] as $article) {
	echo '<article>';
	if ($article['thumb']) {
		echo '<img src="', $article['thumb'], '" alt="', $article['thumb'], '">';
	}

	echo '<h2>', $article['title'], '</h2>';
	echo '<p>', $article['author'], '</p>';
	echo '<p>', $article['summary'], '</p>';
	echo '</article>', "\n";
}
?>
</main>

<? include(__DIR__ . '/ssi/scripts.php'); ?>

</body>

</html>
