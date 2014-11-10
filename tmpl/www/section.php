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
<? include(__DIR__ . '/ssi/scripts.php'); ?>

</head>

<body>

<header>

<div class="sf-wrapper">

<h1><a href="http://<?= Config::WWWHOST ?>/">sacfeed</a></h1>

<button class="sf-navbtn"><hr><hr><hr></button>

<nav>
<?

foreach ($response['sections'] as $section) {
	echo '<a href="http://', Config::WWWHOST, $section['id'], '">', $section['name'], '</a>', "\n";
}

?>
</nav>

</div>

</header>

<main>
<?

$authorURL = 'http://' . Config::IMGHOST . '/v' . Config::VERSION . '.' . Config::MINORVERSION . Config::AUTHORDIR;

$titleMap = $response['titleMap'];
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
	$author = preg_replace('/^(.*)\s+(the\s+\1)$/i', '$2', $author);
	$author = trim($author);

	if ($author === '') {
		foreach ($titleMap as $name => $primary) {
			if (preg_match('/' . $name . '/i', $article['title'])) {
				$last = preg_replace('/^.*\s([^\s]+)$/', '$1', $primary);
				$email = strtolower($primary{0} . $last . '@sacbee.com');
				$author = $primary . ' ' . $email;
				break;
			}
		}
	}

	if (preg_match('/\s+([^@\s]+@[^@\s]+(?:,\s*[^@\s]+@[^@\s]+)*|the\s*sacramento\s*bee)$/i', $author, $m)) {
		$author = preg_replace('/\s+(?:[^@\s]+@[^@\s]+(?:,\s*[^@\s]+@[^@\s]+)*|the\s*sacramento\s*bee)$/i', '', $author);
		$authorLC = strtolower($author);
		if (isset($authorMap[$authorLC])) {
			$file = $authorURL . $authorMap[$authorLC] . '.jpg';
			$profile = '<img class="sf-profile" src="' . $file . '" alt="' . $author . '">';
		} else if (preg_match('/^([^,]+)(?:,|\s+and)\s+/', $author, $first) && isset($authorMap[strtolower($first[1])])) {
			$file = $authorURL . $authorMap[strtolower($first[1])] . '.jpg';
			$profile = '<img class="sf-profile" src="' . $file . '" alt="' . $author . '">';
		}

		$author = '<p class="sf-byline"><span class="sf-name">' . $author . '</span> ' . $m[1] . '</p>';
	} else if ($author !== '') {
		$author = '<p class="sf-byline"><span class="sf-name">' . preg_replace('/^the\s/', 'The ', $author) . '</span></p>';
	}

?>

<article>
<div class="sf-top">
<?= $thumb ?>
<h2><a href="', $article['url'], '"><?= $article['title'] ?></a></h2>
<p class="sf-summary"><?= $article['summary'] ?></p>
<p><a href="<?=$article['url'] ?>">read more</a></p>
</div>
<div class="sf-bottom">
<?= $profile ?>
<?= $author ?>

<p class="sf-date"><?= $date ?></p>
</div>
</article>
<?php

}

?>

</main>

<? include(__DIR__ . '/ssi/async.php'); ?>
<script>
(function(sacfeed) {

sacfeed.load('UI.Section', function() {
	sacfeed.UI.Section().render();
});

})(window.sacfeed);
</script>

</body>

</html>
