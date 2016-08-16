<?php
	include_once 'constants.php';

// Load language files.
if (isset($_GET["lang"])) {
	$current_language = $_GET["lang"];
} else {
	$current_language = "de_DE";
}

$domain = "kirchen-im-web17";
bindtextdomain($domain, "lang");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

putenv("LANG=" . $current_language);
$result = setlocale(LC_ALL, $current_language);
	
// Create database connection.
$options  = array(
		PDO::ATTR_PERSISTENT => true,
);
try {
	$connection = new PDO('mysql:host='.DATABASE_HOSTNAME.';dbname='.DATABASE_NAME.';charset=utf8', DATABASE_USERNAME, DATABASE_PASSWORD, $options);
} catch (PDOException $e) {
	echo 'Database connection failed!';
}

// Countries.
$countries = array(
		'DE' => _('Deutschland'),
		'LI' => _('Liechtenstein'),
		'LU' => _('Luxemburg'),
		'AT' => _('Österreich'),
		'CH' => _('Schweiz')
);

// Denominations.
$denominations = array(
		'alt-katholisch' => _('alt-katholisch'),
		'anglikanisch' => _('anglikanisch'),
		'evangelisch' => _('evangelisch'),
		'freikirchlich' => _('freikirchlich'),
		'katholisch' => _('katholisch'),
		'ökumenisch' => _('ökumenisch'),
		'andere' => _('andere'),
);

// Types.
$types = array(
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
		'Pastoraler Raum' => _('Pastoraler Raum'),
		'Pfarrei' => _('Pfarrei'),
		'Pfarrvikarie' => _('Pfarrvikarie'),
		'andere' => _('andere')
);
$defaultType = 'Kirchengemeinde';

// which websites URLs can be saved
$websites = array(
		'web' => _('Webauftritt'),
		'blog' => _('Blog'),
		'rss' => 'RSS',
		'facebook' => 'Facebook',
		'flickr' => 'Flickr',
		'googlePlus' => 'Google+',
		'instagram' => 'Instagram',
		'soundcloud' => 'Soundcloud',
		'twitter' => 'Twitter',
		'youtube' => 'YouTube',
);

// Must be a subset of $websites.
$preselected = array(
		'web' => _('Webauftritt'),
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'youtube' => 'YouTube',
);

// Must contain the beginning of a URL for the website type.
$websitesStartOfURL = array(
		'web' => '',
		'blog' => '',
		'rss' => '',
		'facebook' => 'https://www.facebook.com/',
		'flickr' => 'https://www.flickr.com/',
		'googlePlus' => 'https://plus.google.com/',
		'instagram' => 'https://www.instagram.com/',
		'soundcloud' => 'https://soundcloud.com',
		'twitter' => 'https://twitter.com/',
		'youtube' => 'https://www.youtube.com/',
);

$networksToCompare = array(
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'youtube' => 'YouTube'
);

$headerLinks = array(
		_('Über das Projekt') => 'index.php',
		_('Gemeinde hinzufügen') => 'add.php',
		_('Karte') => 'map.php',
		_('Tabelle') => 'table.php',
		_('Social-Media-Vergleich') => 'table.php?compare=true',
		_('Statistik') => 'statistics.php',
		_('Offene Daten') => 'data.php',
		_('Tipps und Tricks') => 'links.php'
);

$footerLinks = array(
		_('Impressum') => 'legal.php',
		_('Entwicklung') => 'development.php'
);

$languages_slugs = array(
		'de_DE' => 'de',
		'en_US' => 'en'
);
$languages_names = array(
		'de_DE' => 'Deutsch',
		'en_US' => 'English'
);
