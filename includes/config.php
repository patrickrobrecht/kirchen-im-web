<?php
	include_once 'constants.php';

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
		'DE' => 'Deutschland',
		'AT' => 'Österreich',
		'CH' => 'Schweiz',
);

// Denominations.
$denominations = array(
		'alt-katholisch',
		'anglikanisch',
		'evangelisch',
		'freikirchlich',
		'katholisch',
		'ökumenisch',
		'andere'
);

// Types.
$types = array(
		'Bildungseinrichtung',
		'Bischofskonferenz',
		'Bistum',
		'Citykirche',
		'Dekanat',
		'Jugend',
		'Jugendverband',
		'Hilfswerk',
		'Internetportal',
		'Kirchengemeinde',
		'Kirchenkreis',
		'Kloster',
		'Laienorganisation',
		'Landeskirche',
		'Pastoraler Raum',
		'Pfarrei',
		'Pfarrvikarie',
		'andere'
);
$defaultType = 'Kirchengemeinde';

// which websites URLs can be saved
$websites = array(
		'web' => 'Webseite',
		'blog' => 'Blog',
		'facebook' => 'Facebook',
		'flickr' => 'Flickr',
		'googlePlus' => 'Google+',
		'instagram' => 'Instagram',
		'twitter' => 'Twitter',
		'youtube' => 'YouTube',
);

// Must be a subset of $websites.
$preselected = array(
		'web' => 'Webseite',
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'youtube' => 'YouTube',
);

// Must contain the beginning of a URL for the website type.
$websitesStartOfURL = array(
		'web' => '',
		'blog' => '',
		'facebook' => 'https://www.facebook.com/',
		'flickr' => 'https://www.flickr.com/',
		'googlePlus' => 'https://plus.google.com/',
		'instagram' => 'https://www.instagram.com/',
		'twitter' => 'https://twitter.com/',
		'youtube' => 'https://www.youtube.com/',
);

$networksToCompare = array(
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'youtube' => 'YouTube'
);

$headerLinks = array(
		'Das Projekt' => 'index.php',
		'Gemeinde hinzufügen' => 'add.php',
		'Karte' => 'map.php',
		'Tabelle' => 'table.php',
		'Social-Media-Vergleich' => 'table.php?compare=true',
		'Statistik' => 'statistics.php',
		'Offene Daten' => 'data.php',
		'Tipps und Tricks' => 'links.php'
);

$footerLinks = array(
		'Impressum' => 'legal.php',
		'Entwicklung' => 'development.php'
);
