<?php

namespace Sacfeed\WWW;

use Sacfeed\Config;
use Sacfeed\Request;
use Sacfeed\DB\Article;
use Sacfeed\DB\Author;
use Sacfeed\DB\Section;

class WWWSection extends Request {

	public function __construct($opts) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [
			'section' => [ // section id
				'type' => 'string',
				'default' => '/',
				'regex' => '/^\/(?:[a-z0-9\-]+\/)*$/',
				'required' => true
			]
		];
	}

	public function handle() {
		if (!parent::handle()) {
			return false;
		}

		$sectionID = $this->params['section'];
		if ($sectionID === '/news/') {
			$find = [];
		} else {
			$section = new Section();
			if (!$section->findOne($sectionID)) {
				$this->template = 'error';
				$this->response->notFound();
				return false;
			}

			$find = ['section' => $sectionID];
		}

		$sections = [];
		$cursor = Section::find();
		foreach ($cursor as $record) {
			$section = new Section($record);
			$sections[] = $section->getAPIFields();
		}

		$articles = [];
		$cursor = Article::find($find)->sort(['ts' => -1])->limit(12);
		foreach ($cursor as $record) {
			$article = new Article($record);
			$articles[] = $article->getAPIFields();
		}

		$titleMap = [];
		$authorMap = [];
		$cursor = Author::find();
		foreach ($cursor as $record) {
			$author = new Author($record);
			foreach ($author->names as $name) {
				$authorMap[strtolower($name)] = $author->_id;
				$titleMap[$name] = $author->names[0];
			}
		}

		$this->response->ttl = Config::LONGCACHE;
		$this->response->result['sections'] = $sections;
		$this->response->result['articles'] = $articles;
		$this->response->result['titleMap'] = $titleMap;
		$this->response->result['authorMap'] = $authorMap;
		return true;
	}
}

Request::$requests[__FILE__] = 'WWWSection';
