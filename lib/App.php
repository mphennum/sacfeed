<?php

namespace Sacfeed;

use DateTimeZone;

abstract class App {
	const WWW = 0;
	const API = 1;

	static public $utc;

	static public function init() {
		register_shutdown_function([__CLASS__, 'shutdown']);

		if (!Config::DEVMODE && PHP_SAPI !== 'cli') {
			error_reporting(E_ERROR);
			ob_start();
		}

		self::$utc = new DateTimeZone('UTC');

		Database::init();
		Cache::init();
	}

	static public function handle() {
		$opts = [];

		$opts['host'] = ($_SERVER['HTTP_HOST'] === Config::APIHOST) ? self::API : self::WWW;
		$opts['origin'] = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;
		$opts['method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

		if ($opts['host'] === self::API) {
			$origin = isset($opts['origin']) ? $opts['origin'] : null;
			if (!Config::DEVMODE && $origin !== 'http://' . Config::WWWHOST) {
				// return an error
				exit(0);
			}

			header('Access-Control-Allow-Origin: http://' . Config::WWWHOST);

			if ($opts['method'] === 'OPTIONS') {
				header('Access-Control-Max-Age: 3600');
				header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
				exit(0);
			}
		}

		//$opts['ip'] = isset($_COOKIE['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
		//$opts['agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$opts['secure'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		$opts['dnt'] = (isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT']);
		$opts['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		$opts['uri'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
		$opts['sid'] = isset($_COOKIE['sid']) ? $_COOKIE['sid'] : null;

		if ($opts['method'] === 'POST' || $opts['method'] === 'PUT') {
			$opts['params'] = &$_POST;
		} else {
			$opts['params'] = &$_GET;
		}

		$request = Request::factory($opts);
		$request->handle();
		$output = $request->view();

		if (!Config::DEVMODE) {
			while (ob_get_level() !== 0) {
				ob_end_clean();
			}
		}

		echo $output;
	}

	static public function shutdown() {
		if (!Config::DEVMODE && function_exists('fastcgi_finish_request')) {
			fastcgi_finish_request();
		}

		ignore_user_abort(true);
		set_time_limit(0);

		Database::shutdown();
		Cache::shutdown();

		gc_collect_cycles();
		exit(0);
	}
}
