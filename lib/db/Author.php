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

	static public function cleanID($id) {
		$id = preg_replace('/\..*$/', '', $id);
		$id = strtolower($id);
		$id = str_replace(' ' , '-', $id);
		$id = preg_replace('/[^a-z\-]/', '', $id);
		$id = preg_replace('/\-+/', '-', $id);
		return $id;
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
