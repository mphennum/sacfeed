<?php

namespace Sacfeed;

if ($status['code'] !== 200) {
	echo json_encode($status, Config::DEVMODE ? JSON_PRETTY_PRINT : 0);
} else if (empty($result)) {
	echo '{}';
} else {
	echo json_encode($result, Config::DEVMODE ? JSON_PRETTY_PRINT : 0);
}
