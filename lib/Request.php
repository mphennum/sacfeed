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
		$this->headers = [];
		$this->response = new Response();
	}

	public function handle() {
		if ($this->response->status['code'] !== 200) {
			return false;
		}

		$opts = $this->opts;

		if ($opts['method'] !== $this->method) {
			$this->response->methodNotAllowed('Only "' . $this->method . '" method allowed for this request');
			return false;
		}

		if ($this->method === 'POST') {
			$this->response->created();
		} else if ($this->method === 'PUT' || $this->method === 'DELETE') {
			$this->response->accepted();
		}

		$params = $opts['params'];
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

		if ($opts['method'] === 'GET') {
			$cache = self::getCache($opts['host'], $opts['resource'], $opts['action'], $opts['params']);
			if ($cache !== false) {
				$this->response->cached = true;
				$this->response->status = $cache['status'];
				$this->response->headers = $cache['headers'];
				$this->response->result = $cache['result'];
				return false;
			}
		}

		return true;
	}

	public function view() {
		$opts = $this->opts;
		$status = $this->response->status;
		$headers = $this->response->headers;
		$result = $this->response->result;

		$api = ($opts['host'] === App::API);

		$now = new DateTime('now', App::$utc);
		$format = 'D\, d M Y H:i:s';
		$nowFormat = $now->format($format) . ' UTC';

		if ($this->response->cached) {
			$headers['Date'] = $nowFormat;

			$expires = new DateTime($headers['Expires'], App::$utc);
			$duration = $expires->getTimestamp() - $now->getTimestamp();
			$headers['Cache-Control'] = 'public, max-age=' . $duration;

			$response = $result;
		} else {
			if ($api) {
				if ($status['code'] < 300) {
					$response = null;
					foreach ($result as $resource) {
						if (!is_array($resource) || !empty($resource)) {
							$response = $result;
							break;
						}
					}

					if ($response === null && $this->method === 'GET' && $status['code'] === 200) {
						$this->response->noContent();
						$status = $this->response->status;
					}
				} else {
					$response = $status;
					if ($status['code'] === 405) {
						$headers['Allow'] = 'GET, HEAD';
					} else if ($status['code'] === 301) {
						$headers['Location'] = $result['location'];
					}
				}
			} else {
				$response = $result;
			}

			//$headers['Content-Language'] = 'en-us';
			$headers['Date'] = $nowFormat;

			if ($this->response->ttl < 1) {
				$headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
				$headers['Pragma'] = 'no-cache';
				$headers['Expires'] = 'Mon, 1 Jan 1970 00:00:00 UTC';
			} else {
				$duration = $this->response->ttl;
				$date = new DateTime('now', App::$utc);
				$date->setTimestamp($now->getTimestamp() + $duration);

				$headers['Last-Modified'] = $nowFormat;
				$headers['Cache-Control'] = 'public, max-age=' . $duration;
				$headers['Pragma'] = 'cache';
				$headers['Expires'] = $date->format($format) . ' UTC';

				self::setCache($opts['host'], $opts['resource'], $opts['action'], $opts['params'], [
					'status' => $status,
					'headers' => $headers,
					'result' => $response
				], $this->response->ttl);
			}
		}

		http_response_code($status['code']);

		foreach ($headers as $k => $v) {
			header($k . ': ' . $v);
		}

		if (!$api && $status['code'] > 399) {
			$this->template = 'error';
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

	// cache

	static public function getCache($host, $resource, $action, $params = []) {
		return Cache::get((string) $host . '|' . $resource . '/' . $action, $params);
	}

	static public function setCache($host, $resource, $action, $params = [], $value, $ttl) {
		return Cache::set((string) $host . '|' . $resource . '/' . $action, $params, $value, $ttl, true);
	}

	// factory

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

		// invalid method
		if ($opts['method'] !== 'GET') {
			$request = new Request($opts);
			$request->template = $opts['format'];
			$request->response->methodNotAllowed('Only GET permitted');
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
			$request->response->methodNotAllowed();
			return $request;
		}

		if ($opts['format'] !== null || !empty($opts['params'])) {
			$request = new Request($opts);
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
				$section = '/';
			} else {
				$section = '/' . $opts['resource'] . '/';
			}

			$opts['resource'] = 'section';
			$opts['params'] = ['section' => $section];
			$request = new $class($opts);
			$request->template = 'section';
			return $request;
		}

		$request = new Request($opts);
		$request->response->notFound();
		return $request;
	}

	// params

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
