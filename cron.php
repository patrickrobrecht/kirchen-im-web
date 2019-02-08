<?php
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\SocialMediaUpdater;
use KirchenImWeb\Updaters\LinkCheckUpdater;

require __DIR__ . '/vendor/autoload.php';
if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

// Export into CSV and JSON.
Exporter::getInstance()->export();

// Check for broken links.
$l = new LinkCheckUpdater();
$l->check();

// Get follower data.
header('Content-Type: application/json;charset=utf-8');
echo json_encode(SocialMediaUpdater::getInstance()->cron());
