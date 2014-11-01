<?php

namespace Sacfeed\API;

use Sacfeed\Database;
use Sacfeed\Request;

class ArticleList extends Request {

	public function __construct($opts = []) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [
			'n' => [
				'type' => 'int',
				'default' => 0, // all
				'required' => false
			]
		];
	}

	public function handle() {
	}
}

Request::$map[__FILE__] = 'ArticleList';
