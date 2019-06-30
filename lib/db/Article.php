<?php

namespace Sacfeed\DB;

use MongoDB\BSON\UTCDateTime as MongoDateTime;

use Sacfeed\Config;
use Sacfeed\Database;

class Article extends Record {
	const COLLECTION = 'articles';

	public function __construct($fields = null) {
		parent::__construct(self::COLLECTION, [
			'section' => null,
			'title' => null,
			'subtitle' => null,
			'author' => null,
			'thumb' => null,
			'content' => null,
			'summary' => null,
			'url' => null,
			'ts' => null
		]);

		if ($fields !== null) {
			$this->setFields($fields);
		}
	}

	public function getAPIFields() {
		return [
			'id' => $this->fields['_id'],
			'section' => $this->fields['section'],
			'title' => $this->fields['title'],
			'subtitle' => $this->fields['subtitle'],
			'author' => $this->fields['author'],
			'thumb' => $this->fields['thumb'],
			'content' => $this->fields['content'],
			'summary' => $this->fields['summary'],
			'url' => $this->fields['url'],
			'ts' => $this->fields['ts']->toDateTime()->getTimestamp() * 1000
		];
	}

	public function setJSONFields($section, $json) {
		$this->fields['_id'] = (int) $json['id'];
		$this->fields['section'] = $section;
		$this->fields['title'] = isset($json['title']) ? $json['title'] : null;
		$this->fields['subtitle'] = isset($json['sub_headline']) ? $json['sub_headline'] : null;
		$this->fields['author'] = isset($json['author']) ? $json['author'] : null;
		$this->fields['content'] = isset($json['content']) ? $json['content'] : null;
		$this->fields['summary'] = isset($json['summary']) ? $json['summary'] : null;
		$this->fields['url'] = isset($json['url']) ? $json['url'] : null;

		if (isset($json['pub_date'])) {
			$ts = (int) $json['pub_date'];
			$this->fields['ts'] = new MongoDateTime($ts);
		} else {
			$this->fields['ts'] = new MongoDateTime();
		}

		if ($json['thumb'] === null) {
			$this->fields['thumb'] = self::getThumbFromAssets($json);
		} else {
			$this->fields['thumb'] = $json['thumb'];
		}
	}

	static public function getThumbFromAssets($json) {
		$thumb = null;

		if (isset($json['assets']['Photo'][0])) {
			$photo = $json['assets']['Photo'][0];
			if (isset($photo['thumb_url']) && trim($photo['thumb_url'])) {
				$thumb = $photo['thumb_url'];
			} else {
				$thumb = $photo['url'];
			}
		} else if (isset($json['assets']['Gallery'][0]['images'][0])) {
			$photo = $json['assets']['Gallery'][0]['images'][0];
			if (isset($photo['thumb_url']) && trim($photo['thumb_url'])) {
				$thumb = $photo['thumb_url'];
			} else {
				$thumb = $photo['url'];
			}
		}

		if ($thumb !== null) {
			$url = preg_replace('/\/FREE_[0-9]+\//', '/LANDSCAPE_320/', $thumb);

			$ch = curl_init($url);
			curl_setopt_array($ch, [
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_NOBODY => true,
				CURLOPT_USERAGENT => Config::CURLUSERAGENT,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_MAXREDIRS => 5,
				CURLOPT_HTTPHEADER => Config::$curlHeaders,
			]);

			$header = curl_exec($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);

			if ($header !== false && $info['http_code'] === 200) {
				return $url;
			}
		}

		return $thumb;
	}

	static public function find(array $query = [], array $projection = null, array $sort = null, $limit = null) {
		Section::requested($query['section'] ?? null);
		return Database::find(self::COLLECTION, $query, $projection, $sort, $limit);
	}
}
