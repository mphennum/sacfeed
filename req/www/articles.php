<?php

namespace Sacfeed\WWW;

use Sacfeed\Request;
use Sacfeed\DB\Article;
use Sacfeed\DB\Author;
use Sacfeed\DB\Section;

class Articles extends Request {

	public function handle() {
		$sections = [];
		$cursor = Section::find();
		foreach ($cursor as $record) {
			$section = new Section($record);
			$sections[] = $section->getAPIFields();
		}

		$articles = [];
		$cursor = Article::find(['section' => '/'])->limit(12);
		foreach ($cursor as $record) {
			$article = new Article($record);
			$articles[] = $article->getAPIFields();
		}

		$authorMap = [];
		$cursor = Author::find();
		foreach ($cursor as $record) {
			$author = new Author($record);
			foreach ($author->names as $name) {
				$authorMap[strtolower($name)] = $author->_id;
			}
		}

		$this->response->result['sections'] = $sections;
		$this->response->result['articles'] = $articles;
		$this->response->result['authorMap'] = $authorMap;
	}
}

Request::$requests[__FILE__] = 'Articles';
