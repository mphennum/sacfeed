<?php

namespace Sacfeed\API;

use Sacfeed\Config;
use Sacfeed\Request;
use Sacfeed\DB\Article;
use Sacfeed\DB\Section;

class APIArticleSave extends Request {

	public function __construct($opts = [ ]) {
		parent::__construct($opts);

		$this->method = 'POST';
		$this->authenticate = true;
		$this->params = [
			'section' => [ // section id
				'type' => 'string',
				'regex' => '/^\/(?:[a-z0-9\-]+\/)*$/i',
				'required' => true,
			],
			'article' => [
				'type' => 'object',
				'required' => true,
			]
		];
	}

	public function handle() {
		if (!parent::handle()) {
			return false;
		}

		$section = new Section();
		if (!$section->findOne($this->params['section'])) {
			$this->response->notFound('Section with id "' . $this->params['section'] . '" not found');
			return false;
		}

		$article = new Article();
		$exists = $article->findOne((int) $this->params['article']['id']);
		$article->setJSONFields($this->params['section'], $this->params['article']);

		if ($exists) {
			$article->update(1);
		} else {
			$article->insert(1);
		}

		$this->response->result['success'] = true;
		return true;
	}
}

Request::$requests[__FILE__] = 'APIArticleSave';
