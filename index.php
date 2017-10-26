<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';
if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

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
    return new \KirchenImWeb\Controllers\APIController($container);
};
$container['PageController'] = function ($container) {
    return new \KirchenImWeb\Controllers\PageController($container);
};

$app->group('/', function () {
    $this->get('', function (Request $request, Response $response) {
        return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('de-home'));
    });
    $this->get('sitemap.xml', 'PageController:sitemap');
});

$app->group('/api/', function () {
    $this->get('churches/', 'APIController:churches')->setName('api-churches');
    $this->get('churches/{id}/', 'APIController:church')->setName('api-church');

    $this->get('check/', 'APIController:check');
    $this->get('export/', 'APIController:export');
    $this->get('update/', 'APIController:update');
});

$app->group('/de/', function () {
    $this->get('', 'PageController:index')->setName('de-home');
    $this->get('karte/', 'PageController:map')->setName('de-map');
    $this->get('suche/', 'PageController:search')->setName('de-search');
    $this->get('vergleich/[?sort={sort}]', 'PageController:comparison')->setName('de-comparison');
    $this->get('eintragen/', 'PageController:add')->setName('de-add');
    $this->post('eintragen/', 'PageController:addForm')->setName('de-add-form');
    $this->get('statistik/', 'PageController:stats')->setName('de-stats');
    $this->get('tipps-und-tricks/', 'PageController:links')->setName('de-links');
    $this->get('details/{id}/', 'PageController:details')->setName('de-details');
    $this->get('impressum/', 'PageController:legal')->setName('de-legal');
    $this->get('daten/', 'PageController:data')->setName('de-data');
})->add(function ($request, $response, $next) use ($container) {
    $container->PageController->setLanguage('de_DE', $request);
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
    $this->get('data/', 'PageController:data')->setName('en-data');
})->add(function ($request, $response, $next) use ($container) {
    $container->PageController->setLanguage('en_US', $request);
    $response = $next($request, $response);
    return $response;
});

// Rewrites for URLs of kirchen-im-web.de v2.x
$app->group('/{lang}/', function () {
    $this->get('add.php', function (Request $request, Response $response, $args) {
        return $response->withStatus(301)->withHeader('Location',
            $this->router->pathFor($args['lang'] . '-add'));
    });
    $this->get('map.php', function (Request $request, Response $response, $args) {
        return $response->withStatus(301)->withHeader('Location',
            $this->router->pathFor($args['lang'] . '-map'));
    });
    $this->get('table.php', function (Request $request, Response $response, $args) {
        return $response->withStatus(301)->withHeader('Location',
            $this->router->pathFor($args['lang'] . '-search'));
    });
    $this->get('statistics.php', function (Request $request, Response $response, $args) {
        return $response->withStatus(301)->withHeader('Location',
            $this->router->pathFor($args['lang'] . '-stats'));
    });
    $this->get('links.php', function (Request $request, Response $response) {
        return $response->withStatus(301)->withHeader('Location', $this->router->pathFor('de-links'));
    });
    $this->get('details.php', function (Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        return $response->withStatus(301)->withHeader('Location',
            $this->router->pathFor($args['lang'] . '-details', ['id' => $params['id']]));
    });
});

$app->run();
