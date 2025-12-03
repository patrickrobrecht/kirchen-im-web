<?php

use KirchenImWeb\Controllers\APIController;
use KirchenImWeb\Controllers\FileController;
use KirchenImWeb\Controllers\PageController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {
    $app->redirect('/', '/de/', 301);

    $app->group('/', function (RouteCollectorProxy $group) {
        $group->get('robots.txt', FileController::class . ':robots');
        $group->get('sitemap.xml', FileController::class . ':sitemap')
              ->setName('sitemap');
    });

    $app->group('/api/', function (RouteCollectorProxy $group) {
        $group->get('churches/', APIController::class . ':churches');
        $group->get('churches/{id}/', APIController::class . ':church');
        $group->get('churches/{id}/children/', APIController::class . ':children');
        $group->get('churches/{id}/followers/', APIController::class . ':followers')
            ->setName('followers');
    });

    $app->get('/admin/', PageController::class . ':admin');

    $app->group('/de/', function (RouteCollectorProxy $group) {
        $group->get('', PageController::class . ':index')
              ->setName('de-home');
        $group->get('karte/', PageController::class . ':map')
              ->setName('de-map');
        $group->get('suche/', PageController::class . ':search')
              ->setName('de-search');
        $group->get('vergleich/[?sort={sort}]', PageController::class . ':comparison')
              ->setName('de-comparison');
        $group->get('eintragen/', PageController::class . ':add')
              ->setName('de-add');
        $group->post('eintragen/', PageController::class . ':addForm')
              ->setName('de-add-form');
        $group->get('statistik/', PageController::class . ':stats')
              ->setName('de-stats');
        $group->get('details/{id}/', PageController::class . ':details')
              ->setName('de-details');
        $group->get('impressum/', PageController::class . ':legal')
              ->setName('de-legal');
        $group->get('datenschutzerklaerung/', PageController::class . ':privacy')
              ->setName('de-privacy');
        $group->get('daten/', PageController::class . ':data')
              ->setName('de-data');
        $group->get('opensearch.xml', PageController::class . ':opensearch')
              ->setName('de-opensearch');
    })->add(function ($request, RequestHandler $handler) {
        $this->get(PageController::class)
             ->setLanguage('de_DE', $request);
        return $handler->handle($request);
    });

    $app->group('/en/', function (RouteCollectorProxy $group) {
        $group->get('', PageController::class . ':index')
              ->setName('en-home');
        $group->get('map/', PageController::class . ':map')
              ->setName('en-map');
        $group->get('search/', PageController::class . ':search')
              ->setName('en-search');
        $group->get('comparison/[?sort={sort}]', PageController::class . ':comparison')
              ->setName('en-comparison');
        $group->get('add/', PageController::class . ':add')
              ->setName('en-add');
        $group->post('add/', PageController::class . ':addForm')
              ->setName('en-add-form');
        $group->get('statistics/', PageController::class . ':stats')
              ->setName('en-stats');
        $group->get('details/{id}/', PageController::class . ':details')
              ->setName('en-details');
        $group->get('legal-notice/', PageController::class . ':legal')
              ->setName('en-legal');
        $group->get('privacy/', PageController::class . ':privacy')
              ->setName('en-privacy');
        $group->get('data/', PageController::class . ':data')
              ->setName('en-data');
        $group->get('opensearch.xml', PageController::class . ':opensearch')
              ->setName('en-opensearch');
    })->add(function (Request $request, RequestHandler $handler) {
        $this->get(PageController::class)
             ->setLanguage('en_US', $request);
        return $handler->handle($request);
    });
};
