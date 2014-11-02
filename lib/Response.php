<?php

namespace Sacfeed;

class Response {
	static public $codes = [
		200 => 'OK', // request was successful
		201 => 'Created', // resource has been created
		204 => 'No Content', // no content to return

		301 => 'Moved Permanently', // request can be found at a new location
		304 => 'Not Modified', // resource has not been modified since last request

		// all 4xx status will return a 400 http header
		400 => 'Bad Request', // request had malformed syntax
		401 => 'Unauthorized', // user authentication failed
		403 => 'Forbidden', // unreachable request, authentication wont fix
		404 => 'Not Found', // resource / action cannot be found
		405 => 'Method Not Allowed', // method not allowed
		406 => 'Not Acceptable', // format not allowed, invalid param value
		409 => 'Conflict', // conflict with params
		410 => 'Gone', // resource has been deleted
		411 => 'Length Required', // length param is required
		412 => 'Precondition Failed', // resource is not yet accessible
		416 => 'Range Not Satisfiable', // request out of range for available resources, or min/max value
		429 => 'Too Many Requests', // rate limit hit

		// all 5xx status will return a 500 http header
		500 => 'Internal Server Error', // server side error
		501 => 'Not Implemented', // functionality not yet implemeneted
		502 => 'Bad Gateway', // a gateway has failed (database, cache, etc)
		503 => 'Service Unavailable', // entire service is currently unavailable
		504 => 'Gateway Timeout', // a gateway has timed out (databasse, cache, etc)
		507 => 'Insufficient Storage', // out of database room
		509 => 'Bandwidth Limit Exceeded' // out of bandwidth
	];

	public $result;
	public $status;
	public $ttl;

	public function __construct() {
		$this->result = [];
		$this->status = [
			'code' => 200,
			'message' => self::$codes[200]
		];

		$this->ttl = 0;
	}

	// status

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($code, $reason = null) {
		if (!isset(self::$codes[$code])) {
			$this->internalServerError('Invalid status code: ');
			return;
		}

		$this->status['code'] = $code;
		$this->status['message'] = self::$codes[$code];

		if ($reason === null) {
			unset($this->status['reason']);
		} else {
			$this->status['reason'] = $reason;
		}

		if ($code > 399 || $code < 200) {
			$this->result = [];
			$this->ttl = 0;
		}
	}

	// 200

	public function okay($reason = null) {
		$this->setStatus(200, $reason);
	}

	public function created($reason = null) {
		$this->setStatus(201, $reason);
	}

	public function noContent($reason = null) {
		$this->setStatus(204, $reason);
	}

	// 300

	public function movedPermanently($location, $reason = null) {
		$this->result['location'] = $location;
		$this->setStatus(301, $reason);
	}

	public function notModified($reason = null) {
		$this->setStatus(304, $reason);
	}

	// 400

	public function badRequest($reason = null) {
		$this->setStatus(400, $reason);
	}

	public function unauthorized($reason = null) {
		$this->setStatus(401, $reason);
	}

	public function forbidden($reason = null) {
		$this->setStatus(403, $reason);
	}

	public function notFound($reason = null) {
		$this->setStatus(404, $reason);
	}

	public function methodNotAllowed($reason = null) {
		$this->setStatus(405, $reason);
	}

	public function notAcceptable($reason = null) {
		$this->setStatus(406, $reason);
	}

	public function conflict($reason = null) {
		$this->setStatus(409, $reason);
	}

	public function lengthRequired($reason = null) {
		$this->setStatus(411, $reason);
	}

	public function preconditionFailed($reason = null) {
		$this->setStatus(412, $reason);
	}

	public function rangeNotSatisfiable($reason = null) {
		$this->setStatus(416, $reason);
	}

	public function tooManyRequests($reason = null) {
		$this->setStatus(429, $reason);
	}

	// 500

	public function internalServerError($reason = null) {
		$this->setStatus(500, $reason);
	}

	public function notImplemented($reason = null) {
		$this->setStatus(501, $reason);
	}

	public function badGateway($reason = null) {
		$this->setStatus(502, $reason);
	}

	public function serviceUnavailable($reason = null) {
		$this->setStatus(503, $reason);
	}

	public function gatewayTimeout($reason = null) {
		$this->setStatus(504, $reason);
	}

	public function insufficientStorage($reason = null) {
		$this->setStatus(507, $reason);
	}

	public function bandwidthLimitExceeded($reason = null) {
		$this->setStatus(509, $reason);
	}
}
