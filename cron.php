<?php

use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Updaters\LinkChecker;
use KirchenImWeb\Updaters\SocialMediaUpdater;

require __DIR__ . '/src/autoload.php';

$time = microtime(true);

$socialMediaNetworks = array_keys(Configuration::getWebsiteTypesToCompare());
$argumentNamesForSocialMedia = array_map(static fn($i) => $i . ':', $socialMediaNetworks);
$args = getopt('', array_merge(['links:'], $argumentNamesForSocialMedia));

$database = new Database();
Exporter::run($database);

if (isset($args['links'])) {
    $count = (int)$args['links'];
    if ($count > 0) {
        LinkChecker::run($database, $count);
    }
}

$socialMediaUpdater = new SocialMediaUpdater($database);
foreach ($socialMediaNetworks as $network) {
    if (isset($args[$network])) {
        $count = (int)$args[$network];
        if ($count > 0) {
            $socialMediaUpdater->updateNetwork($network, $count);
        }
    }
}

$duration = (microtime(true) - $time);
echo sprintf('Executed in %.2f seconds.', $duration) . PHP_EOL;
