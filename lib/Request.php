<?php

namespace Sacfeed;

use DateTime;

class Request {
	static public $requests = [];

	// default actions
	static public $actions = [
		'GET' => 'read',
		'POST' => 'create',
		'PUT' => 'update',
		'DELETE' => 'delete'
	];

	public $opts;
	public $method;
	public $params;
	public $template;
	public $response;

	public function __construct($opts = []) {
		$this->opts = $opts;
		$this->method = 'GET';
		$this->params = [];
		$this->response = new Response();
	}

	public function handle() {
		if ($this->response->status['code'] !== 200) {
			return false;
		}

		if ($this->opts['method'] !== $this->method) {
			$this->response->methodNotAllowed('Only "' . $this->method . '" method allowed for this request');
			return false;
		}

		if ($this->method === 'POST') {
			$this->response->created();
		} else if ($this->method === 'PUT' || $this->method === 'DELETE') {
			$this->response->accepted();
		}

		$params = $this->opts['params'];
		foreach ($params as $key => $param) {
			if (!isset($this->params[$key])) {
				$this->response->notAcceptable('Parameter "' . $key . '" not allowed in this context');
				return false;
			}
		}

		foreach ($this->params as $key => $param) {
			$required = $param['required'];
			$exists = array_key_exists($key, $params);

			if (!$required && !$exists) {
				$this->params[$key] = $this->params[$key]['default'];
				continue;
			}

			if ($required && !$exists) {
				$this->response->notAcceptable('Missing required parameter "' . $key . '"');
				return false;
			}

			$type = $param['type'];
			$value = $params[$key];
			if ($type === 'int') {
				if (!is_int($value)) {
					$this->response->notAcceptable('Parameter "' . $key . '" must be an integer');
					return false;
				}

				$value = (int) $value;

				if (isset($param['min']) && $value < $param['min']) {
					$this->response->rangeNotSatisfiable('Parameter "' . $key . '" cannot be less than ' . $param['min']);
					return false;
				}

				if (isset($param['max']) && $value > $param['max']) {
					$this->response->rangeNotSatisfiable('Parameter "' . $key . '" cannot be greater than ' . $param['max']);
					return false;
				}
			} else if ($type === 'string') {
				$value = (string) $value;

				if (isset($param['regex']) && !preg_match($param['regex'], $value)) {
					$this->response->notAcceptable('Parameter "' . $key . '" has an invalid value');
					return false;
				}
			}

			$this->params[$key] = $value;
		}

		return true;
	}

	public function view() {
		$opts = $this->opts;
		$result = $this->response->result;
		$status = $this->response->status;

		$api = ($opts['host'] === App::API);
		if ($api) {
			if ($status['code'] < 300) {
				$empty = true;
				foreach ($result as $resource) {
					if (!is_array($resource) || !empty($resource)) {
						$empty = false;
						$response = $result;
						break;
					}
				}

				if ($empty) {
					if ($this->method === 'GET') {
						$this->response->noContent();
						$status = $this->response->status;
					}

					$response = null;
				}
			} else {
				$response = $status;
				if ($status['code'] === 405) {
					$headers[] = 'Allow: GET, HEAD';
				} else if ($status['code'] === 301) {
					$headers[] = 'Location: ' . $result['location'];
				}
			}
		} else {
			$response = $result;
		}

		$now = new DateTime('now', App::$utc);
		$format = 'D\, d M Y H:i:s';
		$nowFormat = $now->format($format) . ' UTC';

		http_response_code($status['code']);
		$headers = [
			//'Content-Language: en-us',
			'Date: ' . $nowFormat
		];

		if ($this->response->ttl === 0) {
			$headers[] = 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
			$headers[] = 'Pragma: no-cache';
			$headers[] = 'Expires: Mon, 1 Jan 1970 00:00:00 UTC';
		} else {
			$duration = $this->response->ttl - 1;
			$date = new DateTime('now', App::$utc);
			$date->setTimestamp($now->getTimestamp() + $duration);

			$headers[] = 'Last-Modified: ' . $nowFormat;
			$headers[] = 'Cache-Control: public, max-age=' . $duration;
			$headers[] = 'Pragma: cache';
			$headers[] = 'Expires: ' . $date->format($format) . ' UTC';
		}

		foreach ($headers as $header) {
			header($header);
		}

		ob_start('ob_gzhandler');
		include __DIR__ . '/../tmpl/' . ($api ? 'api/' : 'www/') . $this->template . '.php';

		$output = trim(ob_get_contents());
		if (!Config::DEVMODE && !$api) {
			$output = preg_replace('/^\s+/m', '', $output);
			$output = preg_replace('/^\n+$/m', "\n", $output);
		}

		if ($output !== '') {
			$output .= "\n";
			header('Content-Length: ' . strlen($output));
		}

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

		$opts['params'] = $params;

		return ($opts['host'] === App::API) ? self::apiFactory($opts) : self::wwwFactory($opts);
	}

	static private function apiFactory($opts = []) {
		$opts['format'] = isset($opts['format']) ? $opts['format'] : 'json';

		if ($opts['version'] === null || $opts['version'] !== Config::VERSION) {
			$request = new Request($opts);
			$request->template = $opts['format'];
			$request->response->badRequest('Invalid version');
			return $request;
		}

		// no resource
		if ($opts['resource'] === '') {
			$request = new Request($opts);
			$request->template = $opts['format'];
			$request->response->badRequest('No resource given');
			return $request;
		}

		// invalid format
		if (!file_exists(__DIR__ . '/../tmpl/api/' . $opts['format'] . '.php')) {
			$request = new Request($opts);
			$request->template = 'json';
			$request->response->notAcceptable('Format not allowed');
			return $request;
		}

		$resource = explode('/', $opts['resource']);
		$opts['resource'] = array_shift($resource);

		if (!empty($resource)) {
			$final = $resource[count($resource) - 1];
			foreach (self::$actions as $method => $action) {
				// default actions, use method instead
				if ($final === $action) {
					$request = new Request($opts);
					$request->template = $opts['format'];
					$request->response->notFound('Action not found');
					return $request;
				}
			}

			$opts['action'] = implode('/', $resource);
		}

		// invalid method
		if ($opts['method'] !== 'GET') {
			$request = new Request($opts);
			$request->template = $opts['format'];
			$request->response->methodNotAllowed('Only GET permitted');
			return $request;
		}

		// invalid resource
		if (!file_exists(__DIR__ . '/../req/api/' . $opts['resource'] . '/')) {
			$request = new Request($opts);
			$request->template = $opts['format'];
			$request->response->notFound('Resource not found');
			return $request;
		}

		// invalid action
		if (!file_exists(__DIR__ . '/../req/api/' . $opts['resource'] . '/' . $opts['action'] . '.php')) {
			$request = new Request($opts);
			$request->template = $opts['format'];
			$request->response->notFound('Action not found');
			return $request;
		}

		$file = realpath(__DIR__ . '/../req/api/' . $opts['resource'] . '/' . $opts['action'] . '.php');

		if (!isset(self::$requests[$file]) && file_exists($file)) {
			require $file;
		}

		//exit((string) $opts['host']);
		if (isset(self::$requests[$file])) {
			$class = 'Sacfeed\\API\\' . self::$requests[$file];
			$request = new $class($opts);
			$request->template = $opts['format'];
			return $request;
		}

		$request = new Request($opts);
		$request->template = $opts['format'];
		$request->response->badRequest('Invalid request');
		return $request;
	}

	static private function wwwFactory($opts = []) {
		$opts['format'] = isset($opts['format']) ? $opts['format'] : null;

		if ($opts['action'] !== 'read') {
			$request = new Request($opts);
			$request->template = 'error';
			$request->response->methodNotAllowed();
			return $request;
		}

		if ($opts['format'] !== null) {
			$request = new Request($opts);
			$request->template = 'error';
			$request->response->notFound();
			return $request;
		}

		if (!empty($opts['params'])) {
			$request = new Request($opts);
			$request->template = 'error';
			$request->response->notFound();
			return $request;
		}

		$file = realpath(__DIR__ . '/../req/www/section.php');
		if (!isset(self::$requests[$file]) && file_exists($file)) {
			require $file;
		}

		if (isset(self::$requests[$file])) {
			$class = 'Sacfeed\\WWW\\' . self::$requests[$file];
			if ($opts['resource'] === '/' || $opts['resource'] === '') {
				$resource = '/';
			} else {
				$resource = '/' . $opts['resource'] . '/';
			}

			$opts['params'] = ['section' => $resource];
			$request = new $class($opts);
			$request->template = 'section';
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
