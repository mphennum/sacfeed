<?php

namespace Sacfeed;

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="en">

<head>

<? include(__DIR__ . '/ssi/meta.php'); ?>

<title>error 404 - sacfeed</title>

<? include(__DIR__ . '/ssi/styles.php'); ?>
<? include(__DIR__ . '/ssi/scripts.php'); ?>

</head>

<body>

<header>

<div class="sf-wrapper">

<h1><a href="http://<?= Config::WWWHOST ?>/">sacfeed</a></h1>

<button class="sf-navbtn"><hr><hr><hr></button>

<nav></nav>

<button class="sf-queuebtn" style="display: none"></button>

</div>

</header>

<main>

<article>
<div class="sf-top">
<h2>Error <?= $status['code'] ?></h2>
</div>
<div class="sf-bottom">
<p style="font-weight: bold"><?= $status['message'] ?></p>
<?= isset($status['reason']) ? '<p>' . $status['reason'] . '</p>' : '' ?>
</div>
</article>

</main>

<? include(__DIR__ . '/ssi/async.php'); ?>
<script>
(function(sacfeed) {

sacfeed.load('UI.Nav', function() {
	sacfeed.UI.Nav().render();
});

})(window.sacfeed);
</script>

</body>

</html>
