<?php

namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\ParameterChecker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

        if (!$entry) {
            return $response->withStatus(404);
        }

        return self::createJsonResponse($response, Exporter::removeNullValues($entry));
    }

    public function children(Request $request, Response $response, array $args): Response
    {
        $entry = Database::getInstance()->getEntry($args['id']);

        if (!$entry) {
            return $response->withStatus(404);
        }

        $children = Database::getInstance()->getFilteredEntries(
            [
                'parent' => $args['id'],
                'options' => ParameterChecker::extractOptions(''),
            ],
            Configuration::getWebsiteTypes()
        );
        return self::createJsonResponse($response, Exporter::removeNullValues($children));
    }

    public function followers(Request $request, Response $response, array $args): Response
    {
        $entry = Database::getInstance()->getEntry($args['id']);
        if (!$entry) {
            return $response->withStatus(404);
        }

        $followerData = [];
        foreach ($entry['websites'] as $website) {
            if (isset($website['followerHistory'])) {
                $followerData[$website['type']] = $website['followerHistory'];
            }
        }

        $params = $request->getQueryParams();
        if (isset($params['type']) && $params['type'] === 'csv') {
            $websiteTypes = Configuration::getWebsiteTypes();

            $types = [];
            $dateToFollowers = [];
            foreach ($followerData as $type => $followers) {
                $types[$type] = $websiteTypes[$type];

                foreach ($followers as $date => $followerCount) {
                    $date = date_format(date_create($date), 'Y-m-d');
                    if (!array_key_exists($date, $dateToFollowers)) {
                        $dateToFollowers[$date] = [];
                    }
                    $dateToFollowers[$date][$type] = $followerCount;
                }
            }
            ksort($dateToFollowers);

            $body = $response->getBody();
            $header = array_merge([''], array_values($types));
            $body->write(implode(',', $header) . PHP_EOL);
            foreach ($dateToFollowers as $date => $values) {
                $row = [$date];
                foreach (array_keys($types) as $type) {
                    $row[] = $values[$type] ?? '';
                }
                $body->write(implode(',', $row) . PHP_EOL);
            }

            $contentDisposition = sprintf('attachment; filename=%s-followers.csv', $entry['slug']);
            return $response
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Disposition', $contentDisposition);
        }

        return self::createJsonResponse($response, Exporter::removeNullValues($followerData));
    }

    private static function createJsonResponse(Response $response, array $payload): Response
    {
        $response->getBody()
                 ->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
