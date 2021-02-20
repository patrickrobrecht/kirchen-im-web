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
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Symfony\Component\Translation\Translator;

/**
 * Class PageController
 *
 * @package KirchenImWeb\Controllers
 */
class PageController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response, array $args)
    {
        return $this->container->get(Twig::class)->render($response, 'pages/index.html.twig', [
            'title' => _('Das Projekt kirchen-im-web.de'),
            'description' => _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'),
            'recentlyAddedEntries' => Database::getInstance()->getRecentlyAddedEntries()
        ]);
    }

    public function map(Request $request, Response $response, array $args)
    {
        return $this->container->get(Twig::class)->render($response, 'pages/map.html.twig', [
            'title' => _('Karte'),
            'headline' => _('Karte kirchlicher Web- und Social-Media-Auftritte'),
            'description' => _('Unsere Karte zeigt viele Kirchengemeinden mit ihren Web- und Social-Media-Auftritten.')
        ]);
    }

    public function search(Request $request, Response $response, array $args): Response
    {
        $filters = ParameterChecker::extractFilterParameters($request);
        $websites = ParameterChecker::extractFilterWebsites($request);
        return $this->container->get(Twig::class)->render($response, 'pages/table.html.twig', [
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
        return $this->container->get(Twig::class)->render($response, 'pages/table.html.twig', [
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
                $this->container->get(Twig::class)->fetch('email/email-add.html.twig', [
                    'added' => $added
                ])
            );
            $check['data'] = [];
        }
        return $this->addResponse($response, $check['data'], $added, $check['messages']);
    }

    private function addResponse(Response $response, array $data, array $added = [], array $messages = []): Response
    {
        return $this->container->get(Twig::class)->render($response, 'pages/add.html.twig', [
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
        return $this->container->get(Twig::class)->render($response, 'pages/stats.html.twig', [
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
                $this->container->get(Twig::class)->offsetGet('languageSlug') . '-details',
                ['id' => $entry['slug']]
            );
            return $response->withRedirect($redirect);
        }

        $id = Database::getInstance()->getIDForSlug($args['id']);
        $entry = Database::getInstance()->getEntry($id, true);

        if (!$entry) {
            return $this->error($request, $response, $args);
        }

        return $this->container->get(Twig::class)->render($response, 'pages/details.html.twig', [
            'entry' => $entry
        ]);
    }

    public function legal(Request $request, Response $response, array $args)
    {
        return $this->container->get(Twig::class)->render($response, 'pages/legal.html.twig', [
            'title' => _('Impressum'),
            'settings' => Database::getInstance()->getSettings()
        ]);
    }

    public function privacy(Request $request, Response $response, array $args): Response
    {
        return $this->container->get(Twig::class)->render($response, 'pages/privacy.html.twig', [
            'title' => _('Datenschutzerklärung'),
            'settings' => Database::getInstance()->getSettings()
        ]);
    }

    public function data(Request $request, Response $response, array $args): Response
    {
        return $this->container->get(Twig::class)->render($response, 'pages/data.html.twig', [
            'title' => _('Offene Daten')
        ]);
    }

    public function error(Request $request, Response $response): Response
    {
        if (str_starts_with($request->getRequestTarget(), '/en')) {
            $this->setLanguage('en_US', $request);
        } else {
            $this->setLanguage('de_DE', $request);
        }

        return $this->container->get(Twig::class)->render($response, 'default.html.twig', [
            'title' => _('Kirchliche Web- und Social-Media-Auftritte'),
            'headline' => _('Seite nicht gefunden'),
            // phpcs:ignore Generic.Files.LineLength.TooLong
            'text' => _('Leider konnte die gewünschte Seite nicht gefunden werden. Möglicherweise wurde dieser Eintrag gelöscht.')
        ])->withStatus(404);
    }

    public function opensearch(Request $request, Response $response, array $args): Response
    {
        return $this->container->get(Twig::class)->render($response, 'pages/opensearch.xml.twig')
                          ->withHeader('Content-Type', 'text/xml; charset=UTF-8');
    }

    public function admin(Request $request, Response $response, array $args): Response
    {
        $this->setLanguage('de_DE', $request);
        $db = Database::getInstance();
        return $this->container->get(Twig::class)->render($response, 'admin/admin.html.twig', [
            'title' => 'Admin',
            'websitesWithMissingFollowers' => $db->getWebsitesWithMissingFollowers(),
            'websitesError' => $db->getErrorWebsitesByStatusCode()
        ]);
    }

    public function setLanguage($language, Request $request): void
    {
        $this->container->get(Translator::class)->setLocale($language);

        putenv(sprintf('LC_ALL=%s', $language));
        setlocale(LC_ALL, $language);

        $this->container->get(Twig::class)->offsetSet('language', str_replace('_', '-', $language));
        $languageSlug = substr($language, 0, 2);
        $this->container->get(Twig::class)->offsetSet('languageSlug', $languageSlug);

        // Set Menu
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
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

        $this->container->get(Twig::class)->offsetSet('headerMenu', $headerMenuItems);
        $this->container->get(Twig::class)->offsetSet('footerMenu', $footerMenuItems);
    }
}
