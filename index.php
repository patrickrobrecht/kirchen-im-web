<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use KirchenImWeb\Controllers\APIController;
use KirchenImWeb\Controllers\FileController;
use KirchenImWeb\Controllers\PageController;

require __DIR__ . '/src/autoload.php';

// Create and configure Slim app
$settings['addContentLengthHeader'] = false;
$debug = defined('DEBUG') && DEBUG;
$settings['debug'] = $debug;
$settings['displayErrorDetails'] = $debug;

$notFoundHandler = function ($c) {
    return function (Request $request, Response $response) use ($c) {
        return $c->PageController->error($request, $response, []);
    };
};

$app = new \Slim\App([
    'settings'        => $settings,
    'notFoundHandler' => $notFoundHandler
]);

$container = $app->getContainer();
$container['APIController'] = function ($container) {
    return new APIController($container);
};
$container['FileController'] = function ($container) {
    return new FileController($container);
};
$container['PageController'] = function ($container) {
    return new PageController($container);
};

$app->group('/', function () {
    $this->get('', function (Request $request, Response $response) {
        return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('de-home'));
    });
    $this->get('robots.txt', FileController::class . ':robots');
    $this->get('sitemap.xml', FileController::class . ':sitemap')->setName('sitemap');
});

$app->group('/api/', function () {
    $this->get('churches/', APIController::class . ':churches');
    $this->get('churches/{id}/', APIController::class . ':church');
    $this->get('churches/{id}/children/', APIController::class . ':children');
});

$app->get('/admin/', PageController::class . ':admin');

$app->group('/de/', function () {
    $this->get('', 'PageController:index')->setName('de-home');
    $this->get('karte/', 'PageController:map')->setName('de-map');
    $this->get('suche/', 'PageController:search')->setName('de-search');
    $this->get('vergleich/[?sort={sort}]', 'PageController:comparison')->setName('de-comparison');
    $this->get('eintragen/', 'PageController:add')->setName('de-add');
    $this->post('eintragen/', 'PageController:addForm')->setName('de-add-form');
    $this->get('statistik/', 'PageController:stats')->setName('de-stats');
    $this->get('details/{id}/', 'PageController:details')->setName('de-details');
    $this->get('impressum/', 'PageController:legal')->setName('de-legal');
    $this->get('datenschutzerklaerung/', 'PageController:privacy')->setName('de-privacy');
    $this->get('daten/', 'PageController:data')->setName('de-data');
    $this->get('opensearch.xml', 'PageController:opensearch')->setName('de-opensearch');
})->add(function ($request, $response, $next) use ($container) {
    $container['PageController']->setLanguage('de_DE', $request);
    $response = $next($request, $response);
    return $response;
});

$app->group('/en/', function () {
    $this->get('', 'PageController:index')->setName('en-home');
    $this->get('map/', 'PageController:map')->setName('en-map');
    $this->get('search/', 'PageController:search')->setName('en-search');
    $this->get('comparison/[?sort={sort}]', 'PageController:comparison')->setName('en-comparison');
    $this->get('add/', 'PageController:add')->setName('en-add');
    $this->post('add/', 'PageController:addForm')->setName('en-add-form');
    $this->get('statistics/', 'PageController:stats')->setName('en-stats');
    $this->get('details/{id}/', 'PageController:details')->setName('en-details');
    $this->get('legal-notice/', 'PageController:legal')->setName('en-legal');
    $this->get('privacy/', 'PageController:privacy')->setName('en-privacy');
    $this->get('data/', 'PageController:data')->setName('en-data');
    $this->get('opensearch.xml', 'PageController:opensearch')->setName('en-opensearch');
})->add(function ($request, $response, $next) use ($container) {
    $container['PageController']->setLanguage('en_US', $request);
    $response = $next($request, $response);
    return $response;
});

$app->run();
