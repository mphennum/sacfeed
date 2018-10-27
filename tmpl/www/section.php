<?php

namespace Sacfeed;

use DateTime;
use DateTimeZone;

use Sacfeed\DB\Author;

header('Content-Type: text/html; charset=UTF-8');

$opts = [
	'section' => $response['section'],
	'titlemap' => $response['titleMap'],
	'authormap' => $response['authorMap'],
	'articles' => $response['articles']
];

if (!empty($response['articles'])) {
	$opts['first'] = $response['articles'][0]['id'];
	$opts['last'] = $response['articles'][count($response['articles']) - 1]['id'];
}

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

<h1><a href="//<?= Config::WWWHOST ?>/">sacfeed</a></h1>

<button class="sf-navbtn"><hr><hr><hr></button>

<nav>
<?

foreach ($response['sections'] as $section) {
	echo '<a href="//', Config::WWWHOST, $section['id'], '">', $section['name'], '</a>', "\n";
}

?>
</nav>

<button class="sf-queuebtn" style="display: none"></button>

</div>

</header>

<main></main>

<? include(__DIR__ . '/ssi/async.php'); ?>
<script>
(function(sacfeed) {

sacfeed.load('UI.Section', function() {
	sacfeed.UI.Section(<?= json_encode($opts) ?>).render();
});

})(window.sacfeed);
</script>

</body>

</html>
