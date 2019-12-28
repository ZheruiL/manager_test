<?php
define('APP_PATH', __DIR__ . '/');

require(APP_PATH . 'core/Core.php');

$config = require(APP_PATH . 'config/config.php');

(new core\Core($config))->run();