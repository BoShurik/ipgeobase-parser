<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:38
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

use BoShurik\IPGeoBase\Console\ParseCommand;
use BoShurik\IPGeoBase\Console\Application;
use BoShurik\IPGeoBase\Parser\Parser;
use BoShurik\IPGeoBase\Persister\PersisterFactory;

$container = new Container();
$container['name'] = 'IPGeoBase';
$container['version'] = '0.0.1';

$container['event-dispatcher'] = function($c){
    return new EventDispatcher();
};

$container['parser'] = function($c){
    return new Parser($c['event-dispatcher']);
};

$container['persister.factory'] = function($c){
    return new PersisterFactory();
};

$container['console.command.parse.name'] = 'parse';
$container['console.command.parse'] = function($c){
    return new ParseCommand($c['console.command.parse.name'], $c['parser'], $c['persister.factory'], $c['event-dispatcher']);
};

$container['console.application'] = function($c) {
    return new Application($c['name'], $c['version'], $c['console.command.parse']);
};

return $container;