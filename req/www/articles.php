<?php

namespace Sacfeed\WWW;

use Sacfeed\Request;
use Sacfeed\DB\Article;
use Sacfeed\DB\Section;

class Articles extends Request {

	public function handle() {
		$sections = [];
		$cursor = Section::find();
		foreach ($cursor as $record) {
			$section = new Section();
			$section->setFields($record);
			$sections[] = $section->getAPIFields();
		}

		$articles = [];
		$cursor = Article::find();
		foreach ($cursor as $record) {
			$article = new Article();
			$article->setFields($record);
			$articles[] = $article->getAPIFields();
		}

		$this->response->result['articles'] = $articles;
		$this->response->result['sections'] = $sections;
	}
}

Request::$requests[__FILE__] = 'Articles';
