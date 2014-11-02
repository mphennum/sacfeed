<?php

namespace Sacfeed\DB;

use Sacfeed\Database;

class Article extends Record {
	const COLLECTION = 'articles';

	public function __construct($fields = null) {
		parent::__construct(self::COLLECTION, [
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
			'title' => $this->fields['title'],
			'subtitle' => $this->fields['subtitle'],
			'author' => $this->fields['author'],
			'thumb' => $this->fields['thumb'],
			'content' => $this->fields['content'],
			'summary' => $this->fields['summary'],
			'url' => $this->fields['url'],
			'ts' => $this->fields['ts']
		];
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
