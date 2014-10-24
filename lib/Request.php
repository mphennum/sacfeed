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
	public $action;
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

		ob_start('ob_gzhandler');
		$output = '<pre>' . json_encode($opts, JSON_PRETTY_PRINT) . '</pre>';
		ob_end_clean();

		return $output;

		$response = [
			'request' => [
				'resource' => $opts['resource'],
				'action' => $opts['action'],
				'params' => $opts['params'],
				'format' => $opts['format']
			],
			'status' => $this->response->getStatus()
		];

		$result = $this->response->getResult();
		if (!empty($result)) {
			$response['result'] = $result;
		}

		ob_start('ob_gzhandler');
		include __DIR__ . '/../tmpl/' . $this->host . '/' . $this->template . '.php';
		$output = ob_get_contents();
		ob_end_clean();

		$ttl = $response['status']['ttl'];
		if (!$this->cached && $ttl !== 0) {
			Cache::set($this->host . ':' . $this->resource . ':' . $this->action, $this->params, $response, $ttl, true);
		}

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
		return new Request($opts);
	}

	static private function wwwFactory($opts = []) {
		$opts['format'] = isset($opts['format']) ? $opts['format'] : 'html';
		$opts['resource'] = ($opts['resource'] === '') ? 'home' : $opts['resource'];
		return new Request($opts);
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
