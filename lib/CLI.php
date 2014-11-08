<?php

namespace Sacfeed;

use Exception;

class CLI {
	static public $opts = [];
	static private $reserved = [
		'help' => 'This help message',
		'v' => 'verbose'
	];

	static public $verbose = false;
	static public $usleep = 100000; // (micro seconds) sleep timer for title, subtitle, notice, warning, error

	static public $color = [
		'reset' => "\033[0m",
		'black' => "\033[0;30m",
		'grey' => "\033[1;30m",
		'red' => "\033[0;31m",
		'light-red' => "\033[1;31m",
		'green' => "\033[0;32m",
		'light-green' => "\033[1;32m",
		'yellow' => "\033[0;33m",
		'light-yellow' => "\033[1;33m",
		'blue' => "\033[0;34m",
		'light-blue' => "\033[1;34m",
		'purple' => "\033[0;35m",
		'light-purple' => "\033[1;35m",
		'blue-green' => "\033[0;36m",
		'light-blue-green' => "\033[1;36m",
		'light-grey' => "\033[0;37m",
		'white' => "\033[1;37m"
	];

	static public function init($file, $title, $opts = []) {
		$short = '';
		$long = [];
		foreach ($opts as $opt => $message) {
			$len = strlen($opt);
			if ($len === 1 || ($len === 2 && $opt{1} === ':')) {
				if (isset(self::$reserved[$opt{0}]) || isset(self::$reserved[$opt{0} . ':'])) {
					throw new Exception('Cannot used reserved long CLI option "' . $opt . '"');
				}

				$short .= $opt;
			} else {
				if (isset(self::$reserved[$opt])) {
					throw new Exception('Cannot used reserved long CLI option "' . $opt . '"');
				}

				$long[] = $opt;
			}
		}

		foreach (self::$reserved as $reserved => $message) {
			$len = strlen($reserved);
			if ($len === 1 || ($len === 2 && $reserved{1} === ':')) {
				$short .= $reserved;
			} else {
				$long[] = $reserved;
			}
		}

		self::$opts = getopt($short, $long);

		if (isset(self::$opts['help'])) {
			$opts = array_merge(self::$reserved, $opts);
			self::$verbose = true;

			$file = explode('/', $file);
			$file = $file[count($file) - 1];

			CLI::message($title);
			CLI::message('   usage:  ', $file . ' [OPTIONS]');
			foreach ($opts as $opt => $message) {
				$len = strlen($opt);
				if ($len === 1 || ($len === 2 && $opt{1} === ':')) {
					CLI::message('       -' . $opt{0} . '  ', $message);
				} else {
					$spaces = '';
					for ($i = 7 - strlen($opt); $i > 0; --$i) {
						$spaces .= ' ';
					}

					CLI::message($spaces . '--' . $opt . '  ', $message);
				}
			}

			exit(0);
		}

		if (isset(self::$opts['v'])) {
			self::$verbose = true;
		}

		CLI::title($title);
	}

	static public function opt($key) {
		if (isset(self::$opts[$key])) {
			if (self::$opts[$key] === false) {
				return true;
			}

			return self::$opts[$key];
		}

		return false;
	}

	static public function title($message) {
		if (!self::$verbose) {
			return;
		}

		$separator = '';
		for ($i = 0, $n = strlen($message); $i < $n; ++$i) {
			$separator .= '=';
		}

		echo self::$color['light-green'], $message, "\n", $separator;
		self::newline();

		usleep(self::$usleep * 2);
	}

	static public function subtitle($message) {
		if (!self::$verbose) {
			return;
		}

		$separator = '';
		for ($i = 0, $n = strlen($message); $i < $n; ++$i) {
			$separator .= '-';
		}

		echo self::$color['green'], $message, "\n", $separator;
		self::newline();

		usleep(self::$usleep);
	}

	static public function message($message, $second = null, $color = null, $end = '') {
		if (!self::$verbose) {
			return;
		}

		if ($second === null) {
			if ($color === null) {
				$color = self::$color['white'];
			}

			echo $color, $message, self::$color['white'], $end;
			self::newline();
			return;
		}

		if ($color === null) {
			$color = self::$color['light-grey'];
		}

		echo self::$color['white'], $message, $color, $second, self::$color['white'], $end;
		self::newline();
	}

	static public function printr($array = [], $field = null, $tabs = 0, $end = false) {
		if (!self::$verbose) {
			return;
		}

		$spacing = '';
		for ($i = 0; $i < $tabs; ++$i) {
			$spacing .= '    ';
		}

		if ($field === null) {
			self::message('[');
		} else {
			self::message($spacing . $field . ' => [');
		}

		$i = 0;
		$n = count($array);
		++$tabs;
		foreach ($array as $key => $value) {
			++$i;
			$last = ($i === $n);

			if (is_array($value) && !empty($value)) {
				self::printr($value, $key, $tabs, $last);
				continue;
			}

			$color = null;
			if (is_object($value)) {
				$color = self::$color['light-yellow'];
				$value = '[Object]';
			} else if (is_array($value)) {
				$value = '[]';
			} else if (is_string($value)) {
				$color = self::$color['green'];
				$value = '"' . str_replace('"', self::$color['light-yellow'] . '\\"' . self::$color['green'], $value) . '"';
			} else if (is_bool($value)) {
				$color = self::$color['light-blue'];
				$value = ($value ? 'true' : 'false');
			} else if ($value === null) {
				$color = self::$color['light-red'];
				$value = 'null';
			}

			if (strpos($value, "\n") === false) {
				self::message($spacing . '    ' . $key . ' => ', $value, $color, $last ? '' : ',');
				continue;
			}

			$extraspacing = '';
			for ($j = 0, $l = strlen($spacing . '    ' . $key . ' => '); $j < $l; ++$j) {
				$extraspacing .= ' ';
			}

			if (is_string($value)) {
				$extraspacing .= ' '; // for the opening quote
			}

			self::message($spacing . '    ' . $key . ' => ', str_replace("\n", "\n" . $extraspacing, $value), $color, $last ? '' : ',');
		}

		if ($field === null || $end) {
			self::message($spacing . ']');
		} else {
			self::message($spacing . '],');
		}
	}

	static public function newline($force = false) {
		if (!$force && !self::$verbose) {
			return;
		}

		echo self::$color['reset'], "\n";
	}

	static public function notice($message) {
		echo self::$color['blue-green'], $message;
		self::newline(true);

		usleep(self::$usleep);
	}

	static public function warning($message) {
		echo self::$color['light-yellow'], $message;
		self::newline(true);

		usleep(self::$usleep * 2);
	}

	static public function error($message) {
		echo self::$color['light-red'], $message;
		self::newline(true);

		throw new Exception($message);
	}

	// light purple
	static public function input($message) {
		echo self::$color['light-purple'], $message, self::$color['reset'], ' ';
		$fp = fopen('php://stdin', 'r');

		return trim(fgets($fp));
	}
}
