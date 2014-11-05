<?php

namespace Sacfeed\DB;

use Sacfeed\Database;

class Author extends Record {
	const COLLECTION = 'authors';

	public function __construct($fields = null) {
		parent::__construct(self::COLLECTION, [
			'names' => []
		]);

		if ($fields !== null) {
			$this->setFields($fields);
		}
	}

	public function getAPIFields() {
		return [
			'id' => $this->fields['_id'],
			'names' => $this->fields['names']
		];
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
