<?php

namespace KirchenImWeb\Controllers;

use KirchenImWeb\Helpers\Configuration;
use KirchenImWeb\Helpers\Database;
use KirchenImWeb\Helpers\Mailer;
use KirchenImWeb\Helpers\Exporter;
use KirchenImWeb\Helpers\ParameterChecker;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class PageController
 *
 * @package KirchenImWeb\Controllers
 */
class PageController extends TwigController
{
    private $router;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->router = $container['router'];
    }

    public function index(Request $request, Response $response, array $args)
    {
        return $this->twig->render($response, 'pages/index.html.twig', [
            'title' => _('Das Projekt kirchen-im-web.de'),
            'description' => _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'),
            'recentlyAddedEntries' => Database::getInstance()->getRecentlyAddedEntries()
        ]);
    }

    public function map(Request $request, Response $response, array $args)
    {
        return $this->twig->render($response, 'pages/map.html.twig', [
            'title' => _('Karte'),
            'headline' => _('Karte kirchlicher Web- und Social-Media-Auftritte'),
            'description' => _('Unsere Karte zeigt viele Kirchengemeinden mit ihren Web- und Social-Media-Auftritten.')
        ]);
    }

    public function search(Request $request, Response $response, array $args): Response
    {
        $filters = ParameterChecker::extractFilterParameters($request);
        $websites = ParameterChecker::extractFilterWebsites($request);
        return $this->twig->render($response, 'pages/table.html.twig', [
            'title' => _('Suche'),
            'headline' => _('Kirchliche Web- und Social-Media-Auftritte'),
            'description' => _('Suchen Sie hier nach kirchlichen Web- und Social-Media-Auftritten.'),
            'compare' => false,
            'filters' => $filters,
            'websites' => $websites,
            'sort' => ParameterChecker::extractSort($request, 'city'),
            'entries' => Database::getInstance()->getFilteredEntries($filters, $websites)
        ]);
    }

    public function comparison(Request $request, Response $response, array $args): Response
    {
        $filters = ParameterChecker::extractFilterParameters($request);
        $websites = Configuration::getWebsiteTypesToCompare();
        return $this->twig->render($response, 'pages/table.html.twig', [
            'title' => _('Vergleich kirchlicher Social-Media-Auftritte'),
            'headline' => _('Vergleich kirchlicher Social-Media-Auftritte'),
            // phpcs:ignore Generic.Files.LineLength.TooLong
            'description' => _('kirchen-im-web.de vergleicht kirchliche Social-Media-Auftritte anhand ihrer Follower-Zahlen.'),
            'compare' => true,
            'filters' => $filters,
            'websites' => $websites,
            'sort' => ParameterChecker::extractSort($request, 'facebook'),
            'entries' => Database::getInstance()->getFilteredEntries($filters, $websites, true)
        ]);
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $data = ParameterChecker::parseAddFormPreSelectionParameters($request);
        return $this->addResponse($response, $data);
    }

    public function addForm(Request $request, Response $response, array $args): Response
    {
        $check = ParameterChecker::parseAddFormParameters($request);
        $added = [];

        if ($check['correct']) {
            $added = Database::getInstance()->addChurch($check['data']);
            Exporter::run(Database::getInstance());
            Mailer::sendMail(
                'Kirchen im Web: Neuer Eintrag',
                $this->twig->fetch('email/email-add.html.twig', [
                    'added' => $added
                ])
            );
            $check['data'] = [];
        }
        return $this->addResponse($response, $check['data'], $added, $check['messages']);
    }

    private function addResponse(Response $response, array $data, array $added = [], array $messages = []): Response
    {
        return $this->twig->render($response, 'pages/add.html.twig', [
            'title' => _('Gemeinde eintragen'),
            'description' => _('Hier können Sie Ihre Gemeinde zu kirchen-im-web.de hinzufügen.'),
            'data' => $data,
            'added' => $added,
            'messages' => $messages,
            'parents' => Database::getInstance()->getParentEntries()
        ]);
    }

    public function stats(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance();
        return $this->twig->render($response, 'pages/stats.html.twig', [
            'title' => _('Statistik'),
            'description' => _('Statistik zu den Eintragungen auf kirchen-im-web.de'),
            'total' => $db->getTotalCount(),
            'statsByCountry' => $db->getStatsByCountry(),
            'statsByDenomination' => $db->getStatsByDenomination(),
            'statsByType' => $db->getStatsByType(),
            'statsByWebsite' => $db->getStatsByWebsite(),
            'statsHTTPS' => $db->getStatsHTTPS()
        ]);
    }

    public function details(Request $request, Response $response, array $args)
    {
        $id = (int)$args['id'];
        if ($id > 0) {
            // Redirect for old URL.
            $entry = Database::getInstance()->getEntry($id, true);
            $redirect = $this->router->pathFor(
                $this->twig->offsetGet('languageSlug') . '-details',
                ['id' => $entry['slug']]
            );
            return $response->withRedirect($redirect);
        }

        $id = Database::getInstance()->getIDForSlug($args['id']);
        $entry = Database::getInstance()->getEntry($id, true);

        if (!$entry) {
            return $this->error($request, $response, $args);
        }

        return $this->twig->render($response, 'pages/details.html.twig', [
            'entry' => $entry
        ]);
    }

    public function legal(Request $request, Response $response, array $args)
    {
        return $this->twig->render($response, 'pages/legal.html.twig', [
            'title' => _('Impressum'),
            'settings' => Database::getInstance()->getSettings()
        ]);
    }

    public function privacy(Request $request, Response $response, array $args): Response
    {
        return $this->twig->render($response, 'pages/privacy.html.twig', [
            'title' => _('Datenschutzerklärung'),
            'settings' => Database::getInstance()->getSettings()
        ]);
    }

    public function data(Request $request, Response $response, array $args): Response
    {
        return $this->twig->render($response, 'pages/data.html.twig', [
            'title' => _('Offene Daten')
        ]);
    }

    public function error(Request $request, Response $response, array $args): Response
    {
        if (strpos($request->getRequestTarget(), '/en') === 0) {
            $this->setLanguage('en_US', $request);
        } else {
            $this->setLanguage('de_DE', $request);
        }

        return $this->twig->render($response, 'default.html.twig', [
            'title' => _('Kirchliche Web- und Social-Media-Auftritte'),
            'headline' => _('Seite nicht gefunden'),
            // phpcs:ignore Generic.Files.LineLength.TooLong
            'text' => _('Leider konnte die gewünschte Seite nicht gefunden werden. Möglicherweise wurde dieser Eintrag gelöscht.')
        ])->withStatus(404);
    }

    public function opensearch(Request $request, Response $response, array $args): Response
    {
        return $this->twig->render($response, 'pages/opensearch.xml.twig')
                          ->withHeader('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function admin(Request $request, Response $response, array $args): Response
    {
        $this->setLanguage('de_DE', $request);
        $db = Database::getInstance();
        return $this->twig->render($response, 'admin/admin.html.twig', [
            'title' => 'Admin',
            'websitesWithMissingFollowers' => $db->getWebsitesWithMissingFollowers(),
            'websitesError' => $db->getErrorWebsitesByStatusCode()
        ]);
    }

    public function setLanguage($language, Request $request)
    {
        putenv(sprintf('LC_ALL=%s', $language));
        setlocale(LC_ALL, $language);
        $this->twig->offsetSet('language', str_replace('_', '-', $language));
        $languageSlug = substr($language, 0, 2);
        $this->twig->offsetSet('languageSlug', $languageSlug);

        // Set Menu
        $route = $request->getAttribute('route');
        $routeWithoutLanguagePrefix = $route ? substr($route->getName(), 3) : 'home';
        $args = $route ? $route->getArguments() : [];

        $headerMenuItems = [
            [
                'path' => $languageSlug . '-map',
                'text' => _('Karte')
            ], [
                'path' => $languageSlug . '-search',
                'text' => _('Suche')
            ], [
                'path' => $languageSlug . '-comparison',
                'text' => _('Social-Media-Vergleich')
            ], [
                'path' => $languageSlug . '-stats',
                'text' => _('Statistik')
            ]
        ];

        if ($routeWithoutLanguagePrefix) {
            if ($language === 'de_DE') {
                $headerMenuItems = array_merge($headerMenuItems, [
                    [
                        'class' => 'lang_en',
                        'path' => 'en-' . $routeWithoutLanguagePrefix,
                        'args' => $args,
                        'text' => 'English'
                    ]
                ]);
            } elseif ($language === 'en_US') {
                $headerMenuItems = array_merge($headerMenuItems, [
                    [
                        'class' => 'lang_de',
                        'path' => 'de-' . $routeWithoutLanguagePrefix,
                        'args' => $args,
                        'text' => 'Deutsch'
                    ]
                ]);
            }
        }

        $footerMenuItems = [
            [
                'path' => $languageSlug . '-legal',
                'text' => 'Impressum'
            ],
            [
                'path' => $languageSlug . '-privacy',
                'text' => 'Datenschutzerklärung'
            ],
            [
                'path' => $languageSlug . '-data',
                'text' => 'Offene Daten'
            ]
        ];

        $this->twig->offsetSet('headerMenu', $headerMenuItems);
        $this->twig->offsetSet('footerMenu', $footerMenuItems);
    }
}
