<?php

namespace Sacfeed\DB;

use MongoDate;

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
			'section' => $this->fields['section'],
			'title' => $this->fields['title'],
			'subtitle' => $this->fields['subtitle'],
			'author' => $this->fields['author'],
			'thumb' => $this->fields['thumb'],
			'content' => $this->fields['content'],
			'summary' => $this->fields['summary'],
			'url' => $this->fields['url'],
			'ts' => $this->fields['ts']->sec * 1000 + ($this->fields['ts']->usec / 1000)
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
			$this->fields['ts'] = new MongoDate($ts / 1000);
		} else {
			$this->fields['ts'] = new MongoDate();
		}

		if (isset($json['assets']['Photo'][0])) {
			$photo = $json['assets']['Photo'][0];
			if (isset($photo['thumb_url']) && trim($photo['thumb_url'])) {
				$this->fields['thumb'] = $photo['thumb_url'];
			} else {
				$this->fields['thumb'] = $photo['url'];
			}
		} else if (isset($json['assets']['Gallery'][0]['images'][0])) {
			$photo = $json['assets']['Gallery'][0]['images'][0];
			if (isset($photo['thumb_url']) && trim($photo['thumb_url'])) {
				$this->fields['thumb'] = $photo['thumb_url'];
			} else {
				$this->fields['thumb'] = $photo['url'];
			}
		}
	}

	static public function find($query = [], $projection = []) {
		if (isset($query['section'])) {
			Section::requested($query['section']);
		}

		return Database::find(self::COLLECTION, $query, $projection);
	}
}
