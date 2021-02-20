<?php

use KirchenImWeb\Controllers\APIController;
use KirchenImWeb\Controllers\FileController;
use KirchenImWeb\Controllers\PageController;
use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Twig\TwigAssetVersionExtension;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Views\Twig;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;

return [
    App::class => static function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        return AppFactory::create();
    },

    APIController::class => static function () {
        return new APIController();
    },

    FileController::class => static function (ContainerInterface $container) {
        return new FileController($container);
    },

    StreamFactoryInterface::class => static function () {
        return new StreamFactory();
    },

    PageController::class => static function (ContainerInterface $container) {
        return new PageController($container);
    },

    ResponseFactoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(App::class)->getResponseFactory();
    },

    RouteParserInterface::class => static function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    Twig::class => static function (ContainerInterface $container) {
        $twig = Twig::create(__DIR__ . '/../resources/html', [
            'cache' => ( defined('DEBUG') && DEBUG ) ? false : __DIR__ . '/../cache',
        ]);

        $translator = $container->get(Translator::class);
        $twig->addExtension(new TranslationExtension($translator));

        $twig->addExtension(new TwigAssetVersionExtension(__DIR__ . '/../public/assets/rev-manifest.json'));

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
    },

    Translator::class => static function (ContainerInterface $container) {
        $translator = new Translator('en_US', new MessageFormatter(new IdentityTranslator()));

        $translator->addLoader('mo', new MoFileLoader());
        $translator->addResource('mo', __DIR__ . '/lang/en_US/LC_MESSAGES/kirchen-im-web.mo', 'en_US');

        return $translator;
    },
];
