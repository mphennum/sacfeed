<?php

namespace Sacfeed\API;

use Sacfeed\Config;
use Sacfeed\Request;
use Sacfeed\DB\Article;
use Sacfeed\DB\Section;

class APIArticleList extends Request {

	public function __construct($opts = []) {
		parent::__construct($opts);

		$this->method = 'GET';
		$this->params = [
			'n' => [ // number
				'type' => 'int',
				'default' => 12,
				'min' => 1,
				'max' => 12,
				'required' => false
			],
			'a' => [ // after
				'type' => 'int',
				'default' => null,
				'min' => 1,
				'max' => 999999999,
				'required' => false
			],
			's' => [ // since
				'type' => 'int',
				'default' => null,
				'min' => 1,
				'max' => 999999999,
				'required' => false
			],
			'section' => [ // section id
				'type' => 'string',
				'regex' => '/^\/(?:[a-z0-9\-]+\/)*$/i',
				'required' => true
			]
		];
	}

	public function handle() {
		if (!parent::handle()) {
			return false;
		}

		if (isset($this->params['a']) && isset($this->params['s'])) {
			$this->response->conflict('Cannot have both "a" (after) and "s" (since) parameters');
			return false;
		}

		$sectionID = $this->params['section'];
		if ($sectionID === '/news/') {
			$find = [];
		} else {
			$section = new Section();
			if (!$section->findOne($sectionID)) {
				$this->response->notFound('Section with id "' . $sectionID . '" not found');
				return false;
			}

			$find = ['section' => $sectionID];
		}

		if (isset($this->params['a'])) {
			$article = new Article();
			if (!$article->findOne($this->params['a'])) {
				$this->response->notFound('Article with id "' . $this->params['a'] . '" not found');
				return false;
			}

			$find['ts'] = ['$lt' => $article->ts];
		} else if (isset($this->params['s'])) {
			$article = new Article();
			if (!$article->findOne($this->params['s'])) {
				$this->response->notFound('Article with id "' . $this->params['s'] . '" not found');
				return false;
			}

			$find['ts'] = ['$gt' => $article->ts];
		}

		$articles = [];
		$cursor = Article::find($find)->sort(['ts' => -1])->limit($this->params['n']);
		foreach ($cursor as $record) {
			$article = new Article($record);
			$articles[] = $article->getAPIFields();
		}

		$this->response->ttl = Config::SHORTCACHE;
		$this->response->result['articles'] = $articles;
		return true;
	}
}

Request::$requests[__FILE__] = 'APIArticleList';
