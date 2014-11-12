<?php

namespace Sacfeed;

$version = Config::VERSION . '.' . Config::MINORVERSION;

?>
<link href="//<?= Config::IMGHOST ?>/v<?= $version ?>/favicon.png" rel="shortcut icon">
<link href="//fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
<?php
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
