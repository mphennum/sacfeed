<?php

namespace Sacfeed\DB;

use Sacfeed\Database;

class Author extends Record {
	const COLLECTION = 'authors';

	public function __construct($fields = null) {
		parent::__construct(self::COLLECTION, [
			'names' => [],
			'image' => null
		]);

		if ($fields !== null) {
			$this->setFields($fields);
		}
	}

	public function getAPIFields() {
		return [
			'names' => $this->fields['section'],
			'image' => $this->fields['title']
		];
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
