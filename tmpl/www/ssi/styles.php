<!--link href="//<?= Sacfeed\Config::IMGHOST ?>/favicon.png" rel="shortcut icon"-->
<link href="//fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
<?php
if (Sacfeed\Config::DEVMODE) {
	foreach (Sacfeed\Config::$manifest['css'] as $package => $files) {
		foreach ($files as $file) {
			echo '<link href="//', Sacfeed\Config::CSSHOST, '/src/' . $file . '.css" rel="stylesheet">', "\n";
		}
	}
} else {
	foreach (Sacfeed\Config::$manifest['css'] as $package => $files) {
		echo '<link href="//', Sacfeed\Config::CSSHOST, '/min/' . $package . '.css" rel="stylesheet">', "\n";
	}
}
?>
