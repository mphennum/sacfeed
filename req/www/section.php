<?php

namespace Sacfeed\WWW;

use Sacfeed\Request;
use Sacfeed\DB\Article;
use Sacfeed\DB\Author;
use Sacfeed\DB\Section;

class WWWSection extends Request {

	public function __construct($opts) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [
			'section' => [
				'type' => 'string',
				'default' => '/',
				'regex' => '/^(\/[a-z\-]+)*\/$/',
				'required' => true
			],
			'article' => [
				'type' => 'string',
				'default' => null,
				'regex' => '/^[a-z0-9]+(\-[a-z0-9]+)*$/',
				'required' => false
			]
		];
	}

	public function handle() {
		if (!parent::handle()) {
			return false;
		}

		$section = new Section();
		if (!$section->findOne($this->params['section'])) {
			$this->template = 'error';
			$this->response->notFound();
			return false;
		}

		$sections = [];
		$cursor = Section::find();
		foreach ($cursor as $record) {
			$section = new Section($record);
			$sections[] = $section->getAPIFields();
		}

		$articles = [];
		$cursor = Article::find(['section' => $this->params['section']])->limit(12);
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

		return true;
	}
}

Request::$requests[__FILE__] = 'WWWSection';
