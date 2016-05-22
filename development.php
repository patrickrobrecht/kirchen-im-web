<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="de-DE">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Entwicklung von kirchen-im-web.de</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte. Das Projekt kirchen-im-web.de macht diese sichtbar.">
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php displayHeader('development.php'); ?>
	
	<main>
		<h1>Entwicklung</h1>
	
		<article>
			<h2>Entwickler</h2>
			<p>kirchen-im-web.de ist ein Projekt von</p>
			<ul>
				<li><a href="http://joerg-lohrer.de/">Jörg Lohrer</a></li>
				<li><a href="https://patrick-robrecht.de">Patrick Robrecht</a></li>
			</ul>
			<p>Möchtest du bei der Weiterentwicklung mithelfen? Dann melde dich doch bei uns (Kontakt siehe <a href="legal.php">Impressum</a>).</p>
			<p>Der Quelltext dieser Webanwendung ist auf <a href="https://github.com/patrickrobrecht/kirchen-im-web">Github</a> frei verfügbar.</p>
		</article>
		
		<article>
			<h2>Changelog</h2>
			<p>In diesem Changelog sind alle Änderungen im Funktionsumfang erfasst.</p>
			<section>
				<h3>Version 1.3 (2016-04-11)</h3>
				<ul>
					<li>Unterscheidung aller Konfessionen durch verschiedene Farben in der <a href="map.php">Karte</a></li>
				</ul>
			</section>
			<section>
				<h3>Version 1.2 (2016-03-20)</h3>
				<ul>
					<li>Filter nach Name und Co. auch für <a href="table.php?compare=true">Social-Media-Vergleich</a></li>
					<li>erweiterte <a href="statistics.php">Statistiken</a> mit Diagrammen</li>
				</ul>
			</section>
			<section>
				<h3>Version 1.1 (2016-01-30)</h3>
				<ul>
					<li>Speicherung der Daten in einer Datenbank</li>
					<li>Eintragung über eigenes <a href="add.php">Formular</a></li>
					<li><a href="table.php">Filter (in Tabellenansicht)</a> nach Name, PLZ, Ort, Land, Konfession und Gemeindetyp</li>
					<li>Detail-Anzeige für <a href="details.php?id=1">jede eingetragene Gemeinde</a></li>
					<li>Statistik zur <a href="statistics.php">Anzahl der vorhandenen Daten</a></li>
					<li><a href="table.php?compare=true">Social-Media-Vergleich</a> (Likes bei Facebook, Follower bei Twitter und Abonnenten bei YouTube)</li>
				</ul>
			</section>
			<section>
				<h3>Version 1.0 (2015-05-29)</h3>
				<ul>
					<li>Visualisierung der eingetragenden Gemeinden als <a href="map.php">Karte</a>
						<ul>
							<li>realisiert mit Kartenmaterial von <a href="https://www.openstreetmap.org">OpenStreetMap</a> und leaflet.js</li>
							<li>Marker auf der Karte in verschiedene Farben für evangelische und katholische Kirchen sowie Kirchen anderer Konfessionen</li>
						</ul></li>
					<li>Daten als HTML-<a href="table.php">Tabelle</a>
						<ul>
							<li>Filter-Möglichkeit nach Konfession und Netzwerk (über Anker und Klassen im HTML und CSS 3.0)</li>
							<li>Sortierung nach beliebiger Spalte mit tablesorter.js</li>
						</ul></li>
					<li><a href="data.php">Offene Daten</a>: Name, Konfession, Landeskirche bzw. Bistum, Adresse, Web-/Facebook-/Google+-/Twitter-/YouTube-URL
						<ul>
							<li>Eintragung über GoogleDocs-Formular</li>
							<li>automatische Berechnung von Längen- und Breitengrad</li>
						</ul></li>
				</ul>
			</section>
		</article>
			
		<article>
			<h2>Verwendete Programmiersprachen und Bibliotheken</h2>
			<p>Bei der Erstellung dieses Verzeichnisses wurden unter anderem verwendet:</p>
			<ul>
				<li><a href="https://www.w3.org/standards/webdesign/htmlcss">HTML5 und CSS 3.0</a></li>
				<li><a href="https://secure.php.net/">PHP</a></li>
				<li>JavaScript, besonders die Bibliotheken 
					<ul>
						<li><a href="https://jquery.com/">jQuery</a>,</li>
						<li><a href="http://leafletjs.com/">Leaflet</a> (für die Karte),</li>
						<li><a href="http://tablesorter.com/">Tablesorter</a> (für die Sortierung der Tabellen),</li>
						<li><a href="http://www.highcharts.com/">Highcharts</a> (für die Diagramme)</li>
					</ul></li>
			</ul>
		</article>
	</main>
	
	<?php displayFooter('development.php'); ?>
</body>
</html>
