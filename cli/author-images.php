#!/usr/bin/php
<?php

namespace Sacfeed;

use MongoDate;

use Sacfeed\DB\Author;

require __DIR__ . '/../sys/bootstrap.php';

CLI::init('Sacfeed -- author images cli', [
	'n:' => 'author name / list of names',
	'i:' => 'author image/id',
	'r:' => 'author image/id to remove'
]);

$id = CLI::opt('r');
if ($id) {
	$id = Author::cleanID($id);
	$author = new Author();
	if (!$author->findOne($id)) {
		CLI::error('Unable to find an author with id "' . $id . '"');
	}

	$names = $author->names;
	$author->remove();
	CLI::notice('Author "' . $names[0] . '" has been removed');
	exit(0);
}

$names = CLI::opt('n');
if ($names === false) {
	CLI::error('Missing name(s)');
}

$names = preg_split('/\s*,\s*/', $names);

$id = CLI::opt('i');
if ($id === false) {
	$id = Author::cleanID($names[0]);
	CLI::warning('Missing image/id, using "' . $id . '" as image/id');
} else {
	$id = Author::cleanID($id);
}

$author = new Author();
if ($author->findOne($id)) {
	CLI::error('An author with this image/id already exists');
}

$author->_id = $id;

$author->names = $names;

$author->insert();
CLI::notice('Author "' . $names[0] . '" has been inserted');
