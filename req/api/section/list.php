<?php

namespace Sacfeed\API;

use Sacfeed\Request;
use Sacfeed\DB\Section;

class APISectionList extends Request {

	public function __construct($opts = []) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [];
	}

	public function handle() {
		if (!parent::handle()) {
			return false;
		}

		$sections = [];
		$cursor = Section::find();
		foreach ($cursor as $record) {
			$section = new Section($record);
			$sections[] = $section->getAPIFields();
		}

		$this->response->result['sections'] = $sections;
		return true;
	}
}

Request::$requests[__FILE__] = 'APISectionList';
