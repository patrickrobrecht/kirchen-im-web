<?php
namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\ParameterChecker;
use KirchenImWeb\Helpers\SocialMediaUpdater;
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
        $filters = ParameterChecker::getInstance()->extractFilterParameters($request);
        $websites = Configuration::getInstance()->websites;
        $entries = Database::getInstance()->getFilteredEntries($filters, $websites);
        return $response->withJson(Exporter::getInstance()->removeNullValues($entries));
    }

    public function church(Request $request, Response $response, array $args)
    {
        $entry = Database::getInstance()->getEntry($args['id']);
        if ($entry) {
            return $response->withJson(Exporter::getInstance()->removeNullValues($entry));
        } else {
            return $response->withStatus(404);
        }
    }

    public function children(Request $request, Response $response, array $args)
    {
        $entry = Database::getInstance()->getEntry($args['id']);
        if ($entry) {
            $children = Database::getInstance()->getFilteredEntries(
                [
                    'parent' => $args['id'],
                    'options' => ParameterChecker::getInstance()->extractOptions('')
                ],
                Configuration::getInstance()->websites
            );
            return $response->withJson(Exporter::getInstance()->removeNullValues($children));
        } else {
            return $response->withStatus(404);
        }
    }
}
