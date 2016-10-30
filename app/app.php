<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:38
 */

require_once __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/container.php';

return $container['console.application'];