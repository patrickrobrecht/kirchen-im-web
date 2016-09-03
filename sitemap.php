<?php 
	include_once 'includes/config.php';
	
	header('Content-Type: text/xml; charset=UTF-8');
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php
foreach ($languages_slugs as $lang) {

	foreach ($headerLinks as $page) { ?>
	<url>
		<loc>http://kirchen-im-web.de/<?php echo $lang; ?>/<?php echo $page; ?></loc>
		<changefreq>monthly</changefreq>
	</url>
<?php }
	
	foreach ($footerLinks as $page) { ?>
	<url>
		<loc>http://kirchen-im-web.de/<?php echo $lang; ?>/<?php echo $page; ?></loc>
		<changefreq>monthly</changefreq>
	</url>
<?php }

	$statementDetailsPages = $connection->query('SELECT id FROM churches');
	$detailspages = $statementDetailsPages->fetchAll(PDO::FETCH_ASSOC);

	foreach ($detailspages as $page) { ?>
	<url>
		<loc>http://kirchen-im-web.de/<?php echo $lang; ?>/details.php?id=<?php echo $page['id']; ?></loc>
		<changefreq>monthly</changefreq>
	</url>
<?php }
} // END languages ?>
</urlset>