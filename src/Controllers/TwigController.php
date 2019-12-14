<?php

namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Configuration;
use Psr\Container\ContainerInterface;
use Railto\TwigAssetVersionExtension;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extensions_Extension_I18n;

/**
 * Class TwigController
 *
 * @package KirchenImWeb\Controllers
 */
class TwigController
{
    protected $twig;

    /**
     * Initialize Twig and extensions.
     *
     * @param ContainerInterface $container the Slim container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->twig = new Twig(__DIR__ . '/../../theme/html', [
            'cache' => ( defined('DEBUG') && DEBUG ) ? false : __DIR__ . '/../../cache'
        ]);

        // Instantiate and add Slim specific extension
        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $this->twig->addExtension(new TwigExtension($container['router'], $basePath));

        // Add asset extension for Twig.
        $this->twig->addExtension(new TwigAssetVersionExtension(__DIR__ . '/../../assets/rev-manifest.json'));

        // Add I18n extension for Twig and init textdomain and set default language.
        $this->twig->addExtension(new Twig_Extensions_Extension_I18n());
        $textdomain = 'kirchen-im-web';
        bindtextdomain($textdomain, 'lang');
        bind_textdomain_codeset($textdomain, 'UTF-8');
        textdomain($textdomain);

        // Pass global variables to the view.
        $this->twig->offsetSet('domain', $_SERVER['HTTP_HOST']);
        $this->twig->offsetSet('host', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
        $this->twig->offsetSet('currentPath', $container['request']->getUri()->getPath());
        $this->twig->offsetSet('config', Configuration::getInstance());
    }
}
