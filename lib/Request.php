<?php

namespace Sacfeed;

class Request {
	static public $map = [];

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

		$code = $status['code'];
		if ($code === 200) {
			$response = $result;
		} else if ($code === 204) {
			$response = null;
		} else {
			$response = $status;
		}

		ob_start('ob_gzhandler');
		include __DIR__ . '/../tmpl/' . (($opts['host'] === App::API) ? 'api/' : 'www/') . $this->template . '.php';
		$output = ob_get_contents();

		/*if ($opts['host'] === App::WWW) {
			$output .= '<pre>';
			$output .= json_encode($response, JSON_PRETTY_PRINT) . "\n";
			$output .= json_encode($opts, JSON_PRETTY_PRINT) . "\n";
		} else {
			$output .= json_encode($opts, JSON_PRETTY_PRINT) . "\n";
		}*/

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

		if (!isset(self::$map[$file]) && file_exists($file)) {
			require $file;
		}

		//exit((string) $opts['host']);
		if (isset(self::$map[$file])) {
			$class = 'Sacfeed\\API\\' . self::$map[$file];
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
		$opts['format'] = isset($opts['format']) ? $opts['format'] : 'html';
		$opts['resource'] = ($opts['resource'] === '') ? 'articles' : $opts['resource'];

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
