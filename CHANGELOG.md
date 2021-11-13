# kirchen-im-web.de Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## Version 3.8.0 (2021-11-13)

### Added
- Icons for the menu items, replace icons for social networks and other website types
- Add website type "podcast"
- Export of follower history

### Changed
- Update dependencies, including upgrading to Bootstrap 5
- Switch from Gulp to Laravel Mix as build tool
- Replace jQuery based tablesorter with tablesort library (which has no dependencies)
- Remove jQuery
- Improve layout of details page
- Translate month names into German (in the follower history chart)


## Version 3.7.2 (2021-05-27)

### Fixed
* Fix download links and map
* Update dependencies


## Version 3.7.1 (2021-05-13)

### Fixed
* Update dependencies
* Fix list of churches for manual check
* Fix number formatting


## Version 3.7 (2021-03-02)

### Changed
* Update dependencies, including Slim and Twig
* Make number of updated URLs in LinkChecker and SocialMediaUpdater configurable
* Reuse Instagram and Twitter connection
* Refactor code to use PHP 8 features

### Fixed
* Fix update of Facebook follower count (read via Facebook Graph API)
* Fix update of Instagram follower count (based on scraper with login)


## Version 3.6.3 (2019-12-19)

### Fixed
* Map, Search, Details, Add Form: Removed Google+
* Fix update of Facebook follower count
* Comparison: Removed YouTube (see [#31](https://github.com/patrickrobrecht/kirchen-im-web/issues/31))


## Version 3.6.2 (2019-09-06)

### Fixed
* Details: Date format in diagrams


## Version 3.6.1 (2019-08-28)

### Changed
* Updated dependencies


## Version 3.6 (2019-03-20)

### Changed
* Comparison: Removed Google+ (API shutdown)
* Admin page: Show manual checked URLs after 90 days again


## Version 3.5 (2019-02-16)

### Added
* Own page for privacy statement
* Broken link checker to discover changed URLs
* Admin page showing broken links and missing follower data
* Send e-mail notification to administrator when a new church is added

### Changed
* Details: URLs based on the name of the church
    (old id-based URLs redirect to the new ones)
* Show recently added entries on home page instead of open data page
* Don't mark HTTPS links with secure icon (only HTTP marked as non-secure)
* Own column for follower update status (successful or failed)

### Fixed
* Add Form: Fix geolocation
* Comparison: Fix update of Instagram follower count
    (was broken due to changes of the Instagram API)
* Development: Code style checks for CSS, JavaScript and PHP


## Version 3.4 (2018-10-28)

### Added
* Details: breadcrumbs for parent churches
* robots.txt: dependent on production mode
* sitemap.xml: include last modified

### Changed
* Improved design (based on Bootstrap 4 now)
* Add Form: Improve form validation
* Make app ready for installation in a subdirectory
* Development: list required PHP extensions in `composer.json`
* Development: manage JavaScript and build dependencies via `package.json`
* Development: hash-based file-names for assets to handle browser caching

### Removed
* Links: no link list anymore
* Redirects for URLs of kirchen-im-web.de 2.x


## Version 3.3 (2018-01-24)

### Added
* Favicon
* OpenSearch description


## Version 3.2 (2017-12-02)

### Added
* Details, Search: page structured with schema.org (JSON-LF)
* Details: show history of follower data
* Details: show children on map

### Changed
* Development: manage all dependencies via Composer
    (and [asset-packagist.org](https://asset-packagist.org/))
* Map: refactored code, JavaScript into own file
* Minimization of custom CSS and JavaScript files
* Improved styling of forms (validation, fixes for display on small devices),
    CSS grids instead of floats
* API: several improvements, documentation in README
* Update dependencies (Slim, Highcharts)

### Fixed
* Sitemap: no trailing slash redirect for `sitemap.xml`
* Issues in responsive design on mobile devices
* Search, Comparison: ascending order if sorting alphabetically;
    descending order only if sorting by followers


## Version 3.1 (2017-11-05)

### Added
* Comparison: cron for updating the number of Instagram followers (via Instagram API)
* Redirects for URLs without trailing slash
* `cron.php` for cron job (both JSON/CSV export and social media update) 

### Fixed
* Comparison: additional checks for correctness of JSON returned by APIs
* I18n: fixed broken translations
* Development: no redirection to HTTPS on localhost


## Version 3.0 (2017-10-26)

### Changed
* Map: show clusters on start instead of all entries
* Development: load included libraries via nodejs (`package.json`)
* Development: rewrite whole project with Slim and Twig (leading to new URLs)
    redirects from old to new URLs, code cleanup

### Fixed
* Details: display of churches without a street/geolocation. 


## Version 2.3 (2017-03-26)

### Changed
* Add Form: automatic generation of new JSON/CSV export after adding a church
* Check: social media profiles without followers are not listed in the check
    for missing follower data anymore


## Version 2.2 (2016-10-30)

### Added
* API: Requests for returning church data in JSON format

### Fixed
* Add Form: check start of Vimeo URLs
* Comparison: improve cron updating the number of YouTube followers
    if the URL contains `/channel`
* Comparison: improve cron updating the number of Facebook likes
    if the URL contains id instead of slug


## Version 2.1 (2016-09-08)

### Added
* Add Form: Vimeo as website type
* Add Form: museum as type
* Check: consistency checks
* Comparison: cron for updating the number of Google+ followers (via Google API)
* Sitemap: sitemap.xml with all URLs
* Statistics: numbers on HTTPS/HTTP sites

### Fixed
* Comparison: no PHP warnings anymore
* Comparison: file paths in cron job 
* Details, Search: English number format instead of German default 
* Map: display for entries whose name contains a comma


## Version 2.0 (2016-08-13)

### Added
* Add Form: Luxembourg and Liechtenstein as countries
* Add Form: Soundcloud and RSS as website types
* Comparison, Search: number of entries is shown
* I18n: translation into English (changing URLs)

### Changed
* Links: new links in list of web links
* Search: show only the 25 recently added entries if no filter is active


## Version 1.3 (2016-04-11)

### Added
* Map: different colors for all denominations at the map


## Version 1.2 (2016-03-20)

### Added
* Comparison: filters for the social media comparison, too
* Statistics: diagrams


## Version 1.1 (2016-01-30)

### Added
* Details: a page for every church
* Statistics: page with statistics on the number of entries
    per denomination, country, type and website type (table only)
* Comparison: cron for updating the number of Facebook likes (via Facebook Graph API)
* Comparison: cron for updating the number of Twitter followers (via Twitter API)
* Comparison: cron for updating the number of YouTube subscribers (via Google API)

### Changed
* Search: table with filters according to name, postal code, country, denomination and type


## Version 1.0 (2015-05-29)

### Added
* Map: visualization of the churches as a map, realized with [OpenStreetMap](https://www.openstreetmap.org/)
* Search: table with filters according to denomination and Network and sorting to arbitrary column
* Open Data: name, denomination, regional church or diocese, address, web/Facebook/Google+/Twitter/YouTube URL
* Add Form: automatic calculation of longitude and latitude
