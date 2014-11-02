<?php

namespace Sacfeed\DB;

use Sacfeed\Database;

class Article extends Record {
	const COLLECTION = 'articles';

	public function __construct() {
		parent::__construct(self::COLLECTION, [
			'title' => null,
			'subtitle' => null,
			'author' => null,
			'thumb' => null,
			'content' => null,
			'summary' => null,
			'url' => null,
			'published' => null
		]);
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
			'published' => $this->fields['published']
		];
	}

	static public function find($query = [], $projection = []) {
		return Database::find(self::COLLECTION, $query, $projection);
	}
}
