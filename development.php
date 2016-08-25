<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Entwicklung'); ?> - kirchen-im-web.de</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.">
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Entwicklung'); ?></h1>
	
		<article>
			<h2><?php echo _('Entwickler'); ?></h2>
			<p><?php echo _('kirchen-im-web.de ist ein Gemeinschaftsprojekt von'); ?></p>
			<ul>
				<li><a href="http://joerg-lohrer.de/">Jörg Lohrer</a></li>
				<li><a href="https://patrick-robrecht.de">Patrick Robrecht</a></li>
			</ul>
			<p><?php echo _('Möchten Sie bei der Weiterentwicklung mithelfen?'); ?> <a href="legal.php"><?php echo _('Dann schreiben Sie uns!'); ?></a></p>
			<p><?php echo sprintf( _('Der Quelltext dieser Webanwendung ist auf %s frei verfügbar.'), '<a href="https://github.com/patrickrobrecht/kirchen-im-web">Github</a>' ); ?></p>
		</article>
		
		<article>
			<h2><?php echo _('Änderungshistorie'); ?></h2>
			<section>
				<h3><?php echo sprintf( _('Version %s'), '2.0 (2016-08-13)'); ?></h3>
				<ul>
					<li><?php echo _('Übersetzung ins Englische'); ?></li>
				</ul>
			</section>
			<section>
				<h3><?php echo sprintf( _('Version %s'), '1.3 (2016-04-11)'); ?></h3>
				<ul>
					<li><?php echo _('verschiedene Farben für alle Konfessionen in der Karte'); ?></a></li>
				</ul>
			</section>
			<section>
				<h3><?php echo sprintf( _('Version %s'), '1.2 (2016-03-20)'); ?></h3>
				<ul>
					<li><?php echo _('Filter auch für Social-Media-Vergleich'); ?></li>
					<li><?php echo _('Statistik mit Diagrammen'); ?></li>
				</ul>
			</section>
			<section>
				<h3><?php echo sprintf( _('Version %s'), '1.1 (2016-01-30)'); ?></h3>
				<ul>
					<li><?php echo _('Formular für Neueintragungen'); ?></li>
					<li><?php echo _('Tabelle mit Filter nach Name, PLZ, Ort, Land, Konfession und Gemeindetyp'); ?></li>
					<li><?php echo _('Detailseite für jede Gemeinde'); ?></li>
					<li><?php echo _('Statistik zur Anzahl der vorhandenen Daten'); ?></li>
					<li><?php echo _('Social-Media-Vergleich mit Facebook-Likes, Twitter-Follower und YouTube-Abonnenten'); ?>)</li>
				</ul>
			</section>
			<section>
				<h3><?php echo sprintf( _('Version %s'), '1.0 (2015-05-29)'); ?></h3>
				<ul>
					<li><?php echo sprintf( _('Visualisierung der eingetragenden Gemeinden als Karte, realisiert mit %s'), '<a href="https://www.openstreetmap.org">OpenStreetMap</a>'); ?></li>
					<li><?php echo _('Tabelle mit Filter nach Konfession und Netzwerk und Sortierung nach beliebiger Spalte'); ?></li>
					<li><?php echo _('Offene Daten'); ?>:
						<?php echo _('Name, Konfession, Landeskirche bzw. Bistum, Adresse, Web-/Facebook-/Google+-/Twitter-/YouTube-URL'); ?></li>
					<li><?php echo _('automatische Berechnung von Längen- und Breitengrad'); ?></li>
				</ul>
			</section>
		</article>
			
		<article>
			<h2><?php echo _('Verwendete Programmiersprachen und Bibliotheken'); ?></h2>
			<p><?php echo _('Bei der Erstellung dieses Verzeichnisses wurden unter anderem verwendet:'); ?></p>
			<ul>
				<li><a href="https://www.w3.org/standards/webdesign/htmlcss">HTML5, CSS 3.0</a></li>
				<li><a href="https://secure.php.net/">PHP</a></li>
				<li>JavaScript, <?php echo _('besonders die Bibliotheken'); ?>
					<a href="https://jquery.com/">jQuery</a>,
					<a href="http://leafletjs.com/">Leaflet</a>,
					<a href="http://tablesorter.com/">Tablesorter</a>,
					<a href="http://www.highcharts.com/">Highcharts</a></li>
			</ul>
		</article>
	</main>
	
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>
