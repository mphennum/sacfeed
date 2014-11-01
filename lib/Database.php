<?php

namespace Sacfeed;

use MongoClient;

abstract class Database {
	//const SAVE = 0;
	const BATCH = 1;
	const INSERT = 2;
	const UPDATE = 3;
	const REMOVE = 4;

	static public $mongo;
	static private $queue;

	static public function init() {
		self::$queue = [];

		$client = new MongoClient();
		self::$mongo = $client->sacfeed;
		self::$mongo->w = 0;
		self::$mongo->wtimeout = 30000;
	}

	static public function batchInsert($collection, $records) {
		self::$queue[] = [
			'type' => self::BATCH,
			'collection' => $collection,
			'records' => $records
		];
	}

	static public function insert($collection, $record) {
		self::$queue[] = [
			'type' => self::INSERT,
			'collection' => $collection,
			'record' => $record
		];
	}

	static public function update($collection, $where, $record) {
		self::$queue[] = [
			'type' => self::UPDATE,
			'collection' => $collection,
			'where' => $where,
			'record' => $record
		];
	}

	static public function remove($collection, $where) {
		self::$queue[] = [
			'type' => self::REMOVE,
			'collection' => $collection,
			'where' => $where
		];
	}

	static public function shutdown() {
		$batches = [];
		foreach (self::$queue as $command) {
			$type = &$command['type'];
			$collection = &$command['collection'];

			if ($type === self::INSERT) {
				if (!isset($batches[$collection])) {
					$batches[$collection] = [];
				}

				$batches[$collection][] = $command['record'];
				continue;
			}

			if ($type === self::BATCH) {
				if (!isset($batches[$collection])) {
					$batches[$collection] = [];
				}

				$batches[$collection] = array_merge($batches[$collection], $command['records']);
				continue;
			}

			$collection = self::$mongo->$$collection;
			$where = &$command['where'];

			if ($type === self::UPDATE) {
				$collection->update($where, $command['record']);
			} else if ($type === self::REMOVE) {
				$collection->remove($where);
			}
		}

		foreach ($batches as $collection => $batch) {
			if (empty($batch)) {
				continue;
			}

			$collection = self::$mongo->$$collection;
			if (count($batch) === 1) {
				$collection->insert($batch[0]);
			} else {
				$collection->batchInsert($batch, ['continueOnError' => true]);
			}
		}
	}
}
