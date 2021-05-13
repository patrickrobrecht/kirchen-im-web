<?php

namespace KirchenImWeb\Helpers;

/**
 * Class Configuration
 *
 * @package KirchenImWeb\Helpers
 */
class Configuration
{
    public static function getCountries(): array
    {
        return [
            'DE' => _('Deutschland'),
            'LI' => _('Liechtenstein'),
            'LU' => _('Luxemburg'),
            'AT' => _('Österreich'),
            'CH' => _('Schweiz'),
        ];
    }

    public static function getDenominations(): array
    {
        return [
            'alt-katholisch' => _('alt-katholisch'),
            'anglikanisch'   => _('anglikanisch'),
            'evangelisch'    => _('evangelisch'),
            'freikirchlich'  => _('freikirchlich'),
            'katholisch'     => _('katholisch'),
            'ökumenisch'     => _('ökumenisch'),
        ];
    }

    public static function getTypes(): array
    {
        return [
            'Bildungseinrichtung' => _('Bildungseinrichtung'),
            'Bischofskonferenz'   => _('Bischofskonferenz'),
            'Bistum'              => _('Bistum'),
            'Citykirche'          => _('Citykirche'),
            'Dekanat'             => _('Dekanat'),
            'Jugend'              => _('Jugend'),
            'Gemeindeverband'     => _('Gemeindeverband'),
            'Hilfswerk'           => _('Hilfswerk'),
            'Internetportal'      => _('Internetportal'),
            'Kirchengemeinde'     => _('Kirchengemeinde'),
            'Kirchenkreis'        => _('Kirchenkreis'),
            'Kloster'             => _('Kloster'),
            'Laienorganisation'   => _('Laienorganisation'),
            'Landeskirche'        => _('Landeskirche'),
            'Museum'              => _('Museum'),
            'Pastoraler Raum'     => _('Pastoraler Raum/ Pfarreiengemeinschaft/ Seelsorgeeinheit'),
            'Pfarrei'             => _('Pfarrei'),
            'Pfarrvikarie'        => _('Pfarrvikarie'),
            'andere'              => _('andere'),
        ];
    }

    public static function getWebsiteTypes(): array
    {
        return [
            'web'        => _('Webauftritt'),
            'blog'       => _('Blog'),
            'rss'        => 'RSS',
            'facebook'   => 'Facebook',
            'flickr'     => 'Flickr',
            'instagram'  => 'Instagram',
            'soundcloud' => 'Soundcloud',
            'twitter'    => 'Twitter',
            'vimeo'      => 'Vimeo',
            'youtube'    => 'YouTube',
        ];
    }

    public static function getPreselectedWebsiteTypes(): array
    {
        return [
            'web'       => _('Webauftritt'),
            'facebook'  => 'Facebook',
            'instagram' => 'Instagram',
            'twitter'   => 'Twitter',
        ];
    }

    public static function getStartOfWebsiteURL(): array
    {
        return [
            'web'        => '',
            'blog'       => '',
            'rss'        => '',
            'facebook'   => 'https://www.facebook.com/',
            'flickr'     => 'https://www.flickr.com/',
            'instagram'  => 'https://www.instagram.com/',
            'soundcloud' => 'https://soundcloud.com',
            'twitter'    => 'https://twitter.com/',
            'vimeo'      => 'https://vimeo.com/',
            'youtube'    => 'https://www.youtube.com/',
        ];
    }

    public static function getWebsiteTypesForLinkCheck(): array
    {
        /**
         * All except {@link Configuration::getWebsiteTypesToCompare()}
         * and YouTube, which redirects to cookie consent page.
         */
        return [
            'web'        => _('Webauftritt'),
            'blog'       => _('Blog'),
            'rss'        => 'RSS',
            'flickr'     => 'Flickr',
            'soundcloud' => 'Soundcloud',
            'vimeo'      => 'Vimeo',
        ];
    }

    public static function getWebsiteTypesToCompare(): array
    {
        return [
            'facebook'  => 'Facebook',
            'instagram' => 'Instagram',
            'twitter'   => 'Twitter',
        ];
    }

    public static function getWebsitesToCompareColors(): array
    {
        return [
            'facebook'  => '#3b5998',
            'instagram' => '#383838',
            'twitter'   => '#1da1f2',
        ];
    }

    public static function getSortOptions(): array
    {
        $sortOptions = [
            'name'         => _('Name'),
            'postalCode'   => _('PLZ'),
            'city'         => _('Ort'),
            'country'      => _('Land'),
            'denomination' => _('Konfession'),
            'type'         => _('Gemeindetyp'),
        ];

        return array_merge($sortOptions, self::getWebsiteTypesToCompare());
    }

    public static function getLanguages(): array
    {
        return [
            'de_DE' => 'de',
            'en_US' => 'en',
        ];
    }
}
