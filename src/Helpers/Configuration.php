<?php
namespace KirchenImWeb\Helpers;

/**
 * Class Configuration
 *
 * @package KirchenImWeb\Controllers
 */
class Configuration extends AbstractHelper {
    // Countries.
    public $countries;
    public $denominations;
    public $denominations_colors;
    public $types;
    public $defaultType;
    public $websites;
    public $preselectedWebsites;
    public $websitesStartOfURL;
    public $networksToCompare;
    public $sortOptions;
    public $languages_slugs;

    protected function __construct() {
        $this->countries = array(
            'DE' => _('Deutschland'),
            'LI' => _('Liechtenstein'),
            'LU' => _('Luxemburg'),
            'AT' => _('Österreich'),
            'CH' => _('Schweiz')
        );

        $this->denominations = array(
            'alt-katholisch' => _('alt-katholisch'),
            'anglikanisch' => _('anglikanisch'),
            'evangelisch' => _('evangelisch'),
            'freikirchlich' => _('freikirchlich'),
            'katholisch' => _('katholisch'),
            'ökumenisch' => _('ökumenisch'),
            'andere' => _('andere'),
        );

        $this->denominations_colors = array(
            'alt-katholisch' => 'orange',
            'anglikanisch' => 'green',
            'evangelisch' => 'purple',
            'freikirchlich' => 'blue',
            'katholisch' => 'yellow',
            'ökumenisch' => 'red',
            'andere' => 'red',
        );

        $this->types = array(
            'Bildungseinrichtung' => _('Bildungseinrichtung'),
            'Bischofskonferenz' => _('Bischofskonferenz'),
            'Bistum' => _('Bistum'),
            'Citykirche' => _('Citykirche'),
            'Dekanat' => _('Dekanat'),
            'Jugend' => _('Jugend'),
            'Hilfswerk' => _('Hilfswerk'),
            'Internetportal' => _('Internetportal'),
            'Kirchengemeinde' => _('Kirchengemeinde'),
            'Kirchenkreis' => _('Kirchenkreis'),
            'Kloster' => _('Kloster'),
            'Laienorganisation' => _('Laienorganisation'),
            'Landeskirche' => _('Landeskirche'),
            'Museum' => _('Museum'),
            'Pastoraler Raum' => _('Pastoraler Raum'),
            'Pfarrei' => _('Pfarrei'),
            'Pfarrvikarie' => _('Pfarrvikarie'),
            'andere' => _('andere')
        );
        $this->defaultType = 'Kirchengemeinde';

        $this->websites = array(
            'web' => _('Webauftritt'),
            'blog' => _('Blog'),
            'rss' => 'RSS',
            'facebook' => 'Facebook',
            'flickr' => 'Flickr',
            'googlePlus' => 'Google+',
            'instagram' => 'Instagram',
            'soundcloud' => 'Soundcloud',
            'twitter' => 'Twitter',
            'vimeo' => 'Vimeo',
            'youtube' => 'YouTube',
        );

        $this->preselectedWebsites = array(
            'web' => _('Webauftritt'),
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'youtube' => 'YouTube',
        );

        // Must contain the beginning of a URL for the website type.
        $this->websitesStartOfURL = array(
            'web' => '',
            'blog' => '',
            'rss' => '',
            'facebook' => 'https://www.facebook.com/',
            'flickr' => 'https://www.flickr.com/',
            'googlePlus' => 'https://plus.google.com/',
            'instagram' => 'https://www.instagram.com/',
            'soundcloud' => 'https://soundcloud.com',
            'twitter' => 'https://twitter.com/',
            'vimeo' => 'https://vimeo.com/',
            'youtube' => 'https://www.youtube.com/',
        );

        $this->networksToCompare = array(
            'facebook' => 'Facebook',
            'googlePlus' => 'Google+',
            'twitter' => 'Twitter',
            'youtube' => 'YouTube'
        );

        $this->sortOptions = array(
            'name' => _('Name'),
            'postalCode' => _('PLZ'),
            'city' => _('Ort'),
            'country' => _('Land'),
            'denomination' => _('Konfession'),
            'type' => _('Gemeindetyp')
        );
        $this->sortOptions = array_merge($this->sortOptions, $this->networksToCompare);

        $this->languages_slugs = array(
            'de_DE' => 'de',
            'en_US' => 'en'
        );
    }
}
