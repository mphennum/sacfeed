<?php

namespace Sacfeed;

?>
<!--link href="//<?= Config::IMGHOST ?>/favicon.png" rel="shortcut icon"-->
<link href="//fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
<?php
$version = Config::VERSION . '.' . Config::MINORVERSION;
if (Config::DEVMODE) {
	foreach (Config::$manifest['css'] as $package => $files) {
		foreach ($files as $file) {
			echo '<link href="//', Config::CSSHOST, '/v', $version, '/src/' . $file . '.css" rel="stylesheet">', "\n";
		}
	}
} else {
	foreach (Config::$manifest['css'] as $package => $files) {
		echo '<link href="//', Config::CSSHOST, '/v', $version, '/min/' . $package . '.css" rel="stylesheet">', "\n";
	}
}
?>
