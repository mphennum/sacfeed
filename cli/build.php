#!/usr/bin/php
<?php

namespace Sacfeed;

require __DIR__ . '/../sys/bootstrap.php';

CLI::init(__FILE__, 'Sacfeed -- build / minify', [
	'c' => 'clean the minified dir'
]);

$dir = realpath(__DIR__ . '/../');

// options

if (CLI::opt('c')) {
	exec('rm -rf ' . $dir . '/js/min/*');
	exec('rm -rf ' . $dir . '/css/min/*');
	CLI::notice('Minified directories have been cleaned');
	exit(0);
}

// js

CLI::subtitle('Compiling Javascript');

$manifest = Config::$manifest['js'];

// live script

$script = <<<'EOT'
(function() {

window.sacfeed['packages'] = {%s};
window.sacfeed['packageMap'] = {%s};

})();
EOT;

$map = [];
$packages = [];
foreach ($manifest as $package => $files) {
	$pkg = [];
	foreach ($files as $file) {
		$pkg[] = '"' . $file . '"';
		$map[] = '"' . $file . '": "' . $package . '"';
	}

	$packages[] = '"' . $package . '": [' . implode(',', $pkg) . ']';
}

$script = sprintf($script, implode(',', $map), implode(',', $packages));
$tmp = $dir . '/tmp/live.js';
file_put_contents($tmp, $script);

$compiler = $dir . '/bin/compiler.jar';
foreach ($manifest as $package => $files) {
	CLI::message('Compiling package: ', $package);
	$cmd = 'java -jar ' . $compiler;

	if ($package === 'sacfeed') {
		$cmd .= ' --js ' . $tmp;
	}

	foreach ($files as $file) {
		$file = $dir . '/js/src/' . str_replace('.', '/', strtolower($file)) . '.js';
		if (!file_exists($file)) {
			CLI::error('File "' . $file . '" does not exist');
		}

		$cmd .= ' --js ' . $file;
	}

	$cmd .= ' --js_output_file ' . $dir . '/js/min/' . str_replace('.', '/', strtolower($package)) . '.js';

	$mkdir = $dir . '/js/min/' . str_replace('.', '/', strtolower($package));
	$mkdir = preg_replace('/\/[^\/]+$/', '', $mkdir);
	if (!file_exists($mkdir)) {
		if (!mkdir($mkdir, 0775, true)) {
			CLI::error('mkdir failed for: ' . $mkdir);
		}
	}

	exec($cmd);
}

unlink($tmp);

// css

CLI::subtitle('Compiling CSS');

$manifest = Config::$manifest['css'];

$compiler = $dir . '/bin/yuicompressor-2.4.8.jar';
foreach ($manifest as $package => $files) {
	CLI::message('Compiling package: ', $package);


	$tmp = $dir . '/tmp/' . $package . '.css';
	$src = '';
	foreach ($files as $file) {
		$src .= file_get_contents($dir . '/css/src/' . $file . '.css');
	}

	file_put_contents($tmp, $src);

	exec('java -jar ' . $compiler . ' ' . $tmp . ' -o ' . $dir . '/css/min/' . $package . '.css --type css --charset utf-8');

	unlink($tmp);
}
