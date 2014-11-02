<?php

namespace Sacfeed\API;

use Sacfeed\Request;
use Sacfeed\DB\Section;

class SectionList extends Request {

	public function __construct($opts = []) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [];
	}

	public function handle() {
		$cursor = Section::find();

		$sections = [];
		foreach ($cursor as $record) {
			$section = new Section();
			$section->setFields($record);
			$sections[] = $section->getAPIFields();
		}

		$this->response->result['sections'] = $sections;
	}
}

Request::$map[__FILE__] = 'SectionList';
