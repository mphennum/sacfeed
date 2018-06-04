<?php

namespace Sacfeed;

use Exception;
use MongoDB\Driver\WriteConcern as MongoWriteConcern;
use MongoDB\Driver\BulkWrite as MongoBulkWrite;
use MongoDB\Driver\Manager as MongoManager;
use MongoDB\Driver\Command as MongoCommand;
use MongoDB\Driver\Query as MongoQuery;

abstract class Database {

	//const SAVE = 0;
	const BATCH = 1;
	const INSERT = 2;
	const UPDATE = 3;
	const REMOVE = 4;

	const WRITECONCERN = 0;
	const WRITETIMEOUT = 30000;

	static private $queue;
	static private $client;

	static public function init() {
		self::$queue = [];
		self::$client = new MongoManager(
			Config::DBHOST,
			[
				'username' => Config::DBUSER,
				'password' => Config::DBPASS,
				'w' => self::WRITECONCERN,
				'wTimeoutMS' => self::WRITETIMEOUT
			]
		);
	}

	// commands

	static public function count($collection, array $query = []) {
		$cursor = self::$client->executeReadCommand(
			Config::DBNAME,
			new MongoCommand([
				'count' => $collection,
				'query' => $query
			])
		);

		return $cursor->toArray()[0]->n;
	}

	static public function distinct($collection, $field, array $query = []) {
		$cursor = self::$client->executeReadCommand(
			Config::DBNAME,
			new MongoCommand([
				'distinct' => $collection,
				'key' => $field,
				'query' => $query
			])
		);

		return $cursor->toArray()[0]->values;
	}

	static public function find($collection, array $query = [], array $projection = null, array $sort = null, $limit = null) {
		$opts = [];

		if ($projection !== null) {
			$opts['projection'] = $projection;
		}

		if ($sort !== null) {
			$opts['sort'] = $sort;
		}

		if ($limit !== null) {
			$opts['limit'] = $limit;
		}

		$cursor = self::$client->executeQuery(
			Config::DBNAME . '.' . $collection,
			new MongoQuery($query, $opts)
		);

		$cursor->setTypeMap(['root' => 'array']);
		return $cursor;
	}

	static public function findOne($collection, array $query = [], array $projection = null) {
		$results = self::find($collection, $query, $projection, null, 1)->toArray();
		return $results[0] ?? null;
	}

	static public function batchInsert($collection, array $records = [], $w = 0) {
		if (empty($records)) {
			return;
		}

		$seen = [];
		$n = count($records);
		for ($i = 0; $i < $n; ++$i) {
			$record = $records[$i];

			$id = $record['_id'];
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

		$bulk = new MongoBulkWrite(['ordered' => false]);
		for ($i = 0; $i < $n; ++$i) {
			$bulk->insert($records[$i]);
		}

		$opts = [];
		if ($w !== self::WRITECONCERN) {
			$opts['writeConcern'] = new MongoWriteConcern($w, self::WRITETIMEOUT);
		}

		self::$client->executeBulkWrite(Config::DBNAME . '.' . $collection, $bulk, $opts);
	}

	static public function insert($collection, array $record = [], $w = 0) {
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

		$opts = [];
		if ($w !== self::WRITECONCERN) {
			$opts['writeConcern'] = new MongoWriteConcern($w, self::WRITETIMEOUT);
		}

		self::$client->executeWriteCommand(
			Config::DBNAME,
			new MongoCommand([
				'insert' => $collection,
				'documents' => [$record]
			]),
			$opts
		);
	}

	static public function update($collection, $query = [], $record = [], $w = 0, $multi = false) {
		if (empty($record)) {
			return;
		}

		if ($w === 0) {
			self::$queue[] = [
				'type' => self::UPDATE,
				'collection' => $collection,
				'query' => $query,
				'record' => $record,
				'multi' => $multi
			];

			return;
		}

		self::$mongo->$collection->update($query, $record, ['w' => $w, 'multiple' => $multi]);
	}

	static public function remove($collection, $query = [], $w = 0, $multi = false) {
		if ($w === 0) {
			self::$queue[] = [
				'type' => self::REMOVE,
				'collection' => $collection,
				'query' => $query,
				'w' => $w,
				'multi' => $multi
			];

			return;
		}

		if ($w === false) {
			$w = 0;
		}

		self::$mongo->$collection->remove($query, ['w' => $w, 'justOne' => !$multi]);
	}

	static public function shutdown() {
		$batches = [];
		foreach (self::$queue as $command) {
			$type = $command['type'];
			$collection = $command['collection'];

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
				self::update($collection, $command['query'], $command['record'], false, $command['multi']);
			} else if ($type === self::REMOVE) {
				self::remove($collection, $command['query'], false, $command['multi']);
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
