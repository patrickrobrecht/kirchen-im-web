<?php

namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\ParameterChecker;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class APIController
 *
 * @package KirchenImWeb\Controllers
 */
class APIController
{

    public function __construct(ContainerInterface $container)
    {
    }

    // Data API

    public function churches(Request $request, Response $response, array $args)
    {
        $filters = ParameterChecker::extractFilterParameters($request);
        $websites = Configuration::getWebsiteTypes();
        $entries = Database::getInstance()->getFilteredEntries($filters, $websites);
        return $response->withJson(Exporter::removeNullValues($entries));
    }

    public function church(Request $request, Response $response, array $args): Response
    {
        $entry = Database::getInstance()->getEntry($args['id']);
        if ($entry) {
            return $response->withJson(Exporter::removeNullValues($entry));
        }

        return $response->withStatus(404);
    }

    public function children(Request $request, Response $response, array $args): Response
    {
        $entry = Database::getInstance()->getEntry($args['id']);
        if ($entry) {
            $children = Database::getInstance()->getFilteredEntries(
                [
                    'parent' => $args['id'],
                    'options' => ParameterChecker::extractOptions('')
                ],
                Configuration::getWebsiteTypes()
            );
            return $response->withJson(Exporter::removeNullValues($children));
        }

        return $response->withStatus(404);
    }
}
