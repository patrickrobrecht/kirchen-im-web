<?php
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\SocialMediaUpdater;

require __DIR__ . '/vendor/autoload.php';
if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

Exporter::getInstance()->export();

header('Content-Type: application/json;charset=utf-8');
echo json_encode(SocialMediaUpdater::getInstance()->cron());
