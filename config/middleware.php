<?php

use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return static function (App $app) {
    $app->addRoutingMiddleware();
    $app->add(TwigMiddleware::createFromContainer($app, Twig::class));

    $debug = defined('DEBUG') && DEBUG;
    $app->addErrorMiddleware($debug, $debug, $debug);
};
