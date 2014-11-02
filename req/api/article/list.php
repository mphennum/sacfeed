<?php

namespace Sacfeed\API;

use Sacfeed\Config;
use Sacfeed\Database;
use Sacfeed\Request;

class ArticleList extends Request {

	public function __construct($opts = []) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [
			'n' => [ // number
				'type' => 'int',
				'default' => 100,
				'min' => 1,
				'max' => 100,
				'required' => false
			],
			'a' => [ // after
				'type' => 'string',
				'default' => null,
				'regex' => '/^[a-z0-9\-\_]{' . Config::IDLEN . '}$/i',
				'required' => false
			],
			's' => [ // since
				'type' => 'string',
				'default' => null, // all
				'regex' => '/^[a-z0-9\-\_]{' . Config::IDLEN . '}$/i',
				'required' => false
			]
		];
	}

	public function handle() {
		if (!parent::handle()) {
			return false;
		}

		if (isset($this->params['a']) && isset($this->params['s'])) {
			$this->response->conflict('Cannot have both "a" and "s" parameters');
			return false;
		}

		return true;
	}
}

Request::$map[__FILE__] = 'ArticleList';
