<?php

namespace Sacfeed\DB;

use Sacfeed\Database;

class Section extends Record {
	const COLLECTION = 'sections';

	public function __construct($fields = null) {
		parent::__construct(self::COLLECTION, [
			'name' => null
		]);

		if ($fields !== null) {
			$this->setFields($fields);
		}
	}

	public function getAPIFields() {
		return [
			'id' => $this->fields['_id'],
			'name' => $this->fields['name']
		];
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
