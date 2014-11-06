<?php

namespace Sacfeed\DB;

use MongoDate;

use Sacfeed\Database;

class Section extends Record {
	const COLLECTION = 'sections';

	public function __construct($fields = null) {
		parent::__construct(self::COLLECTION, [
			'name' => null,
			'ts' => null
		]);

		if ($fields !== null) {
			$this->setFields($fields);
		}
	}

	public function getAPIFields() {
		return [
			'id' => $this->fields['_id'],
			'name' => $this->fields['name'],
			'ts' => $this->fields['ts']->sec * 1000 + ($this->fields['ts']->usec / 1000)
		];
	}

	public function findOne($id) {
		if (parent::findOne($id)) {
			self::requested($id);
			return true;
		}

		return false;
	}

	static public function requested($id = null) {
		Database::update(self::COLLECTION, ($id === null) ? [] : ['_id' => $id], ['$set' => ['ts' => new MongoDate()]], 0, true);
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
