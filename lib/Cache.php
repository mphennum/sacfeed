<?php

namespace Sacfeed;

use Memcached;

abstract class Cache {
	const SET = 0;
	const DEL = 1;

	static private $queue;
	static private $memcached;

	static public function init() {
		self::$queue = [];

		self::$memcached = new Memcached('sacfeed');
		self::$memcached->setOptions([
			//Memcached::OPT_TCP_NODELAY => true,
			//Memcached::OPT_RECV_TIMEOUT => 100000,
			//Memcached::OPT_SEND_TIMEOUT => 100000,
			//Memcached::OPT_SERVER_FAILURE_LIMIT => 25,
			//Memcached::OPT_CONNECT_TIMEOUT => 100,
			//Memcached::OPT_RETRY_TIMEOUT => 300,
			//Memcached::DISTRIBUTION => Memcached::DISTRIBUTION_CONSISTENT,
			//Memcached::OPT_REMOVE_FAILED_SERVERS => true,
			Memcached::OPT_HASH => Memcached::HASH_MURMUR,
			Memcached::OPT_SERIALIZER => Memcached::SERIALIZER_IGBINARY
		]);

		if (empty(self::$memcached->getServerList())) {
			foreach (Config::$cacheServers as $host) {
				self::$memcached->addServer($host, 11211);
			}
		}
	}

	static public function get($key, $params = []) {
		return self::$memcached->get(self::createKey($key, $params));
	}

	static public function set($key, $params = [], $value, $ttl = Config::SHORTCACHE, $queue = false) {
		$key = self::createKey($key, $params);

		if ($queue) {
			self::$queue[] = [
				'type' => self::SET,
				'key' => $key,
				'value' => $value,
				'ttl' => $ttl
			];

			return true;
		}

		return self::$memcached->set($key, $value, $ttl - 1);
	}

	static public function delete($key, $params = [], $queue = false) {
		$key = self::createKey($key, $params);

		if ($queue) {
			self::$queue[] = [
				'type' => self::DEL,
				'key' => $key
			];

			return true;
		}

		return self::$memcached->delete($key);
	}

	static public function createKey($key, $params = []) {
		$key = 'sacfeed:' . $key;
		foreach ($params as $k => $v) {
			$key = ':' . $k . '=' . (string) $v;
		}

		return $key;
	}

	// shutdown

	static public function shutdown() {
		foreach (self::$queue as $item) {
			if ($item['type'] === self::SET) {
				self::$memcached->set($item['key'], $item['value'], $item['ttl'] - 1);
			} else if ($item['type'] === self::DEL) {
				self::$memcached->delete($item['key']);
			}
		}
	}
}
