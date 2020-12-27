<?php

use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Updaters\LinkChecker;
use KirchenImWeb\Updaters\SocialMediaUpdater;

require __DIR__ . '/src/autoload.php';

$time = microtime(true);
$args = getopt('l:s:');

$database = new Database();
Exporter::run($database);

if (isset($args['l'])) {
    $count = (int)$args['l'];
    if ($count === 0) {
        echo 'Invalid argument -l.' . PHP_EOL;
    } else {
        LinkChecker::run($database, $count);
    }
}

if (isset($args['s'])) {
    $count = (int)$args['s'];
    if ($count === 0) {
        echo 'Invalid argument -s.' . PHP_EOL;
    } else {
        SocialMediaUpdater::run($database, $count);
    }
}

$duration = (microtime(true) - $time);
echo sprintf('Executed in %.2f seconds.', $duration) . PHP_EOL;
