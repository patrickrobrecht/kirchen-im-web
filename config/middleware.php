<?php

use KirchenImWeb\Controllers\PageController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return static function (App $app) {
    $app->addRoutingMiddleware();
    $app->add(TwigMiddleware::createFromContainer($app, Twig::class));

    $debug = defined('DEBUG') && DEBUG;
    if (!$debug) {
        $errorMiddleware = $app->addErrorMiddleware(false, false, false);
        $errorMiddleware->setDefaultErrorHandler(
            function (
                Request $request,
                Throwable $exception,
                bool $displayErrorDetails,
                bool $logErrors,
                bool $logErrorDetails,
                ?LoggerInterface $logger = null
            ) use ($app) {
                return $app->getContainer()->get(PageController::class)->error(
                    $request,
                    $app->getResponseFactory()->createResponse()
                );
            }
        );
    }
};
