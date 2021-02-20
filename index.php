<?php

use DI\Container;
use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Twig\TwigAssetVersionExtension;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use KirchenImWeb\Controllers\APIController;
use KirchenImWeb\Controllers\FileController;
use KirchenImWeb\Controllers\PageController;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;

require __DIR__ . '/src/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$debug = defined('DEBUG') && DEBUG;
$app->addErrorMiddleware($debug, $debug, $debug);

$container->set('APIController', function () {
    return new APIController();
});
$container->set('FileController', function (ContainerInterface $app) {
    return new FileController($app);
});
$container->set('PageController', function (ContainerInterface $app) {
    return new PageController($app);
});
$container->set('view', static function (ContainerInterface $app) {
    $twig = Twig::create(__DIR__ . '/theme/html', [
        'cache' => ( defined('DEBUG') && DEBUG ) ? false : __DIR__ . '/../../cache',
    ]);

    $translator = new Translator(
        'en_US',
        new MessageFormatter(new IdentityTranslator())
    );
    $translator->addLoader('mo', new MoFileLoader());
    $translator->addResource('mo', __DIR__ . '/lang/en_US/LC_MESSAGES/kirchen-im-web.mo', 'en_US');
    $twig->addExtension(new TranslationExtension($translator));

    $twig->addExtension(new TwigAssetVersionExtension(__DIR__ . '/assets/rev-manifest.json'));

    $twig->offsetSet('domain', $_SERVER['HTTP_HOST']);
    $twig->offsetSet('host', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
    $twig->offsetSet('config', [
        'countries' => Configuration::getCountries(),
        'denominations' => Configuration::getDenominations(),
        'types' => Configuration::getTypes(),
        'websiteTypes' => Configuration::getWebsiteTypes(),
        'startOfWebsiteURL' => Configuration::getStartOfWebsiteURL(),
        'websitesTypesToCompare' => Configuration::getWebsiteTypesToCompare(),
        'websitesToCompareColors' => Configuration::getWebsitesToCompareColors(),
        'sortOptions' => Configuration::getSortOptions(),
        'languages' => Configuration::getLanguages(),
    ]);

    return $twig;
});
$app->add(TwigMiddleware::createFromContainer($app));

$app->group('/', function (RouteCollectorProxy $group) {
    $group->get('', function (Request $request, Response $response, array $args) {
        return $response->withStatus(301)
                        ->withHeader('Location', $this->router->pathFor('de-home'));
    });
    $group->get('robots.txt', FileController::class . ':robots');
    $group->get('sitemap.xml', FileController::class . ':sitemap')
          ->setName('sitemap');
});

$app->group('/api/', function (RouteCollectorProxy $group) {
    $group->get('churches/', APIController::class . ':churches');
    $group->get('churches/{id}/', APIController::class . ':church');
    $group->get('churches/{id}/children/', APIController::class . ':children');
});

$app->get('/admin/', PageController::class . ':admin');

$app->group('/de/', function (RouteCollectorProxy $group) {
    $group->get('', 'PageController:index')
          ->setName('de-home');
    $group->get('karte/', 'PageController:map')
          ->setName('de-map');
    $group->get('suche/', 'PageController:search')
          ->setName('de-search');
    $group->get('vergleich/[?sort={sort}]', 'PageController:comparison')
          ->setName('de-comparison');
    $group->get('eintragen/', 'PageController:add')
          ->setName('de-add');
    $group->post('eintragen/', 'PageController:addForm')
          ->setName('de-add-form');
    $group->get('statistik/', 'PageController:stats')
          ->setName('de-stats');
    $group->get('details/{id}/', 'PageController:details')
          ->setName('de-details');
    $group->get('impressum/', 'PageController:legal')
          ->setName('de-legal');
    $group->get('datenschutzerklaerung/', 'PageController:privacy')
          ->setName('de-privacy');
    $group->get('daten/', 'PageController:data')
          ->setName('de-data');
    $group->get('opensearch.xml', 'PageController:opensearch')
          ->setName('de-opensearch');
})->add(function ($request, RequestHandler $handler) use ($container) {
    $this->get('PageController')
         ->setLanguage('de_DE', $request);
    return $handler->handle($request);
});

$app->group('/en/', function (RouteCollectorProxy $group) {
    $group->get('', 'PageController:index')
          ->setName('en-home');
    $group->get('map/', 'PageController:map')
          ->setName('en-map');
    $group->get('search/', 'PageController:search')
          ->setName('en-search');
    $group->get('comparison/[?sort={sort}]', 'PageController:comparison')
          ->setName('en-comparison');
    $group->get('add/', 'PageController:add')
          ->setName('en-add');
    $group->post('add/', 'PageController:addForm')
          ->setName('en-add-form');
    $group->get('statistics/', 'PageController:stats')
          ->setName('en-stats');
    $group->get('details/{id}/', 'PageController:details')
          ->setName('en-details');
    $group->get('legal-notice/', 'PageController:legal')
          ->setName('en-legal');
    $group->get('privacy/', 'PageController:privacy')
          ->setName('en-privacy');
    $group->get('data/', 'PageController:data')
          ->setName('en-data');
    $group->get('opensearch.xml', 'PageController:opensearch')
          ->setName('en-opensearch');
})->add(function (Request $request, RequestHandler $handler) {
    $this->get('PageController')
         ->setLanguage('en_US', $request);
    return $handler->handle($request);
});

$app->run();
