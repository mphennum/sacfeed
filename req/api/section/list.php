<?php

namepsace Sacfeed\API;

class Section {

	public function __construct() {
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

Request::$map[__FILE__] = 'Section';
