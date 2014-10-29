<?php

namespace Sacfeed\WWW;

use Sacfeed\Request;

class Article extends Request {

	public function handle() {
		//$this->response->result = ['abc' => 123];
	}
}

Request::$map[__FILE__] = 'Article';
