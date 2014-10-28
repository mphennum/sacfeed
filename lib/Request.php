<?php

namespace Sacfeed;

class Request {
	static public $map = [];

	static public $actions = [
		'GET' => 'read',
		'POST' => 'create',
		'PUT' => 'update',
		'DELETE' => 'delete'
	];

	public $opts;
	public $template;
	public $response;

	public function __construct($opts = []) {
		$this->opts = &$opts;
		$this->response = new Response($opts);
	}

	public function handle() {
		// do nothing
	}

	public function view() {
		$opts = &$this->opts;

		$result = &$this->response->result;
		$status = &$this->response->status;

		ob_start('ob_gzhandler');
		include __DIR__ . '/../tmpl/' . ($opts['host'] === App::API ? 'api/' : 'www/') . $this->template . '.php';
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	static public function factory($opts = []) {
		$opts['action'] = isset(self::$actions[$opts['method']]) ? self::$actions[$opts['method']] : null;

		// resource & format

		$uri = explode('?', $opts['uri']);
		$uri = trim($uri[0], '/');

		$parts = explode('.', $uri);
		if (count($parts) === 1) {
			$opts['resource'] = $parts[0];
		} else {
			$opts['format'] = array_pop($parts);
			$opts['resource'] = implode('.', $parts);
		}

		// params

		$params = [];
		foreach ($opts['params'] as $key => $value) {
			$params[rawurldecode($key)] = self::decodeParam($value);
		}

		$opts['params'] = &$params;

		return ($opts['host'] === App::API) ? self::apiFactory($opts) : self::wwwFactory($opts);
	}

	static private function apiFactory($opts = []) {
		$opts['format'] = isset($opts['format']) ? $opts['format'] : 'json';

		$request = new Request($opts);
		$request->template = $opts['format'];
		$request->response->notImplemented();
		return $request;
	}

	static private function wwwFactory($opts = []) {
		$opts['format'] = isset($opts['format']) ? $opts['format'] : 'html';
		$opts['resource'] = ($opts['resource'] === '') ? 'article' : $opts['resource'];

		if ($opts['action'] !== 'read') {
			$request = new Request($opts);
			$request->template = 'error';
			$request->response->methodNotAllowed();
			return $request;
		}

		if ($opts['format'] !== 'html') {
			$request = new Request($opts);
			$request->template = 'error';
			$request->response->notAcceptable('Format not supported.');
			return $request;
		}

		$file = realpath(__DIR__ . '/../req/www/' . $opts['resource'] . '.php');
		if (!isset(self::$map[$file]) && file_exists($file)) {
			require $file;
		}

		if (isset(self::$map[$file])) {
			$class = 'Sacfeed\\WWW\\' . self::$map[$file];
			$request = new $class($opts);
			$request->template = 'article';
			return $request;
		}

		$request = new Request($opts);
		$request->template = 'error';
		$request->response->notFound();
		return $request;
	}

	static public function decodeParam($param) {
		$param = rawurldecode($param);

		if ($param === '' || $param === 'true') {
			return true;
		}

		if ($param === 'false') {
			return false;
		}

		if ($param === 'null') {
			return null;
		}

		if (preg_match('/^-?[0-9]+$/', $param)) {
			return (int) $param;
		}

		if (preg_match('/^-?[0-9]*\.[0-9]+$/', $param)) {
			return (float) $param;
		}

		if (preg_match('/^\[[^,]+(,[^,]*)*\]$/', $param)) {
			$params = explode(',', trim($param, '[]'));
			foreach ($params as &$param) {
				$param = self::decodeParam($param);
			}

			return $params;
		}

		return $param;
	}
}
