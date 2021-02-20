<?php

namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Database;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class FileController
 *
 * @package KirchenImWeb\Controllers
 */
class FileController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function robots(Request $request, Response $response, array $args): Response
    {
        return $this->container->get('view')->render($response, 'files/robots.txt.twig', [
            'production' => PRODUCTION
        ])->withHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(Request $request, Response $response, array $args): Response
    {
        return $this->container->get('view')->render($response, 'files/sitemap.xml.twig', [
            'churches' => Database::getInstance()->getAllChurchesWithLastUpdate()
        ])->withHeader('Content-Type', 'text/xml; charset=UTF-8');
    }
}
