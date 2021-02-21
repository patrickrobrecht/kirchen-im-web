<?php

namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\ParameterChecker;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class APIController
 *
 * @package KirchenImWeb\Controllers
 */
class APIController
{
    public function churches(Request $request, Response $response, array $args)
    {
        $filters = ParameterChecker::extractFilterParameters($request);
        $websites = Configuration::getWebsiteTypes();
        $entries = Database::getInstance()->getFilteredEntries($filters, $websites);
        return self::createJsonResponse($response, Exporter::removeNullValues($entries));
    }

    public function church(Request $request, Response $response, array $args): Response
    {
        $entry = Database::getInstance()->getEntry($args['id']);
        if ($entry) {
            return self::createJsonResponse($response, Exporter::removeNullValues($entry));
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
            return self::createJsonResponse($response, Exporter::removeNullValues($children));
        }

        return $response->withStatus(404);
    }

    private static function createJsonResponse(Response $response, array $payload): Response
    {
        $response->getBody()
                 ->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
