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

	// commands

	static public function findOne() {

	}

	static public function find() {

	}

	static public function distinct() {

	}

	static public function batchInsert($collection, $records = [], $w = 0) {
		if (empty($records)) {
			return;
		}

		$seen = [];
		foreach ($records as $record) {
			$id = &$record['_id'];
			if (isset($seen[$id])) {
				throw new Exception('Duplicate ID found in batch insert');
			}

			$seen[$id] = true;
		}

		if ($w === 0) {
			self::$queue[] = [
				'type' => self::BATCH,
				'collection' => $collection,
				'records' => $records,
				'w' => 0
			];

			return;
		}

		if ($w === false) {
			$w = 0;
		}

		self::$mongo->$collection->batchInsert($records, ['continueOnError' => true, 'w' => $w]);
	}

	static public function insert($collection, $record = [], $w = 0) {
		if (empty($record)) {
			return;
		}

		if ($w === 0) {
			self::$queue[] = [
				'type' => self::INSERT,
				'collection' => $collection,
				'record' => $record
			];

			return;
		}

		if ($w === false) {
			$w = 0;
		}

		self::$mongo->$collection->insert($record, ['w' => $w]);
	}

	static public function update($collection, $where = [], $record = [], $w = 0, $multi = false) {
		if (empty($record)) {
			return;
		}

		if ($w === 0) {
			self::$queue[] = [
				'type' => self::UPDATE,
				'collection' => $collection,
				'where' => $where,
				'record' => $record,
				'multi' => $multi
			];

			return;
		}

		self::$mongo->$collection->update($where, $record, ['w' => $w, 'multiple' => $multi]);
	}

	static public function remove($collection, $where = [], $w = 0, $multi = false) {
		if ($w === 0) {
			self::$queue[] = [
				'type' => self::REMOVE,
				'collection' => $collection,
				'where' => $where,
				'w' => $w,
				'multi' => $multi
			];

			return;
		}

		if ($w === false) {
			$w = 0;
		}

		self::$mongo->$collection->remove($where, ['w' => $w, 'justOne' => !$multi]);
	}

	static public function shutdown() {
		$batches = [];
		foreach (self::$queue as $command) {
			$type = &$command['type'];
			$collection = &$command['collection'];

			// auto batch

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

			// update / remove

			if ($type === self::UPDATE) {
				self::update($collection, $command['where'], $command['record'], false, $command['multi']);
			} else if ($type === self::REMOVE) {
				self::remove($collection, $command['where'], false, $command['multi']);
			}
		}

		// batches

		foreach ($batches as $collection => $batch) {
			if (empty($batch)) {
				continue;
			}

			if (count($batch) === 1) {
				self::insert($collection, $batch[0], false);
			} else {
				self::batchInsert($collection, $batch, false);
			}
		}
	}
}
