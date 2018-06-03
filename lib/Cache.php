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

		if (count(self::$memcached->getServerList()) === 0) {
			foreach (Config::$cacheServers as $host) {
				self::$memcached->addServer($host, 11211);
			}
		}
	}

	static public function get($key, $params = []) {
		return self::$memcached->get(self::createKey($key, $params));
	}

	static public function set($key, $params = [], $value, $ttl = Config::SHORTCACHE, $queue = true) {
		if ($queue) {
			self::$queue[] = [
				'type' => self::SET,
				'key' => $key,
				'params' => $params,
				'value' => $value,
				'ttl' => $ttl
			];

			return true;
		}

		if ($ttl < Config::MICROCACHE) {
			$ttl = Config::MICROCACHE;
		}

		return self::$memcached->set(self::createKey($key, $params), $value, $ttl - 1);
	}

	static public function del($key, $params = [], $queue = true) {
		if ($queue) {
			self::$queue[] = [
				'type' => self::DEL,
				'key' => $key,
				'params' => $params
			];

			return true;
		}

		return self::$memcached->delete(self::createKey($key, $params));
	}

	static public function createKey($key, $params = []) {
		ksort($params);
		return 'sacfeed|' . $key . '|' . serialize($params);
	}

	// shutdown

	static public function shutdown() {
		for ($i = 0, $n = count(self::$queue); $i < $n; ++$i) {
			$item = self::$queue[$i];
			if ($item['type'] === self::SET) {
				self::set($item['key'], $item['params'], $item['value'], $item['ttl'], false);
			} else if ($item['type'] === self::DEL) {
				self::del($item['key'], $item['params'], false);
			}
		}
	}
}
