<?php

namespace Sacfeed\DB;

use Exception;

use Sacfeed\Config;
use Sacfeed\Database;

class Record {
	static private $uuids = [];

	private $collection;
	protected $fields;

	public function __construct($collection, $fields = []) {
		$this->collection = &$collection;
		$this->fields = array_merge(['_id' => null], $fields);
	}

	// fields

	public function genUUID() {
		$this->fields['_id'] = self::uuid($this->collection);
	}

	public function __isset($key) {
		return array_key_exists($key, $this->fields);
	}

	public function __get($key) {
		if (!array_key_exists($key, $this->fields)) {
			throw new Exception('Cannot get undefined field "' . $key . '" for collection "' . $this->collection . '"');
		}

		return $this->fields[$key];
	}

	public function __set($key, $value) {
		if (!array_key_exists($key, $this->fields)) {
			throw new Exception('Cannot set undefined field "' . $key . '" for collection "' . $this->collection . '"');
		}

		$this->fields[$key] = $value;
	}

	public function getAPIFields() {
		return [];
	}

	public function getFields() {
		return $this->fields;
	}

	public function setFields($fields = []) {
		$seen = [];

		foreach ($fields as $key => $value) {
			$seen[$key] = true;
			if (!array_key_exists($key, $this->fields)) {
				throw new Exception('Cannot set undefined field "' . $key . '" for collection "' . $this->collection . '"');
			}
		}

		foreach ($this->fields as $key => $value) {
			if (!isset($seen[$key])) {
				throw new Exception('Missing field "' . $key . '" for setFields on collection "' . $this->collection . '"');
			}
		}

		$this->fields = $fields;
	}

	// db commands

	public function findOne($id) {
		$this->fields = Database::findOne($this->collection, ['_id' => $id]);
		return ($this->fields === null);
	}

	public function insert($w = 0) {
		if ($this->fields['_id'] === null) {
			$this->genUUID();
		}

		Database::insert($this->collection, $this->fields, $w);
	}

	public function update($w = 0) {
		Database::update($this->collection, ['_id' => $this->fields['_id']], $this->fields, $w, false);
	}

	public function remove($w = 0) {
		Database::remove($this->collection, ['_id' => $this->fields['_id']], $w, false);
	}

	// uuid

	static public function uuid($collection, $length = Config::IDLEN) {
		$max = strlen(Config::$chars) - 1;
		$exists = true;
		while ($exists) {
			$id = '';
			for ($i = 0; $i < $length; ++$i) {
				$id .= Config::$chars{mt_rand(0, $max)};
			}

			if (isset(self::$uuids[$id])) {
				continue;
			}

			self::$uuids[$id] = true;

			if (Database::findOne($collection, ['_id' => $id], ['_id' => true]) !== null) {
				continue;
			}

			$exists = false;
		}

		return $id;
	}
}
