<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="de-DE">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Kirchliche Webauftritte und Social-Media-Auftritte</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte. Das Projekt kirchen-im-web.de macht diese sichtbar.">
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php displayHeader('index.php'); ?>
	
	<main>
		<h1>Das Projekt kirchen-im-web.de</h1>

		<article id="info">
			<h2>Das ist kirchen-im-web.de</h2>
			<figure>
				<a href="map.php"><img alt="Karte" src="images/screenshot.png"></a>
				<figcaption>Die <a href="map.php">Karte</a> zeigt die Standorte von Gemeinden im deutschprachigen Raum. Dabei sind alt-katholische orange, anglikanische grün, evangelische lila, freikirchliche blau, katholische gelb und andere/ökumenische rot dargestellt.</figcaption>
			</figure>
			<p>Wir wollen kirchliche Social-Media-Aktivitäten sichtbar machen und so zeigen, dass viele Kirchengemeinden, Landeskirchen und Bistümer auch im Web Öffentlichtkeitsarbeit machen.
			 	Wir stellen die Kirchen dabei sowohl <a href="map.php">als Karte</a> als <a href="table.php">tabellarisch</a> dar. Die Tabelle ermöglicht dabei u.a. ein Filtern nach Konfession, PLZ oder Ort.
				Im <a href="table.php?compare=true">Social-Media-Vergleich</a> werden die Followerzahlen der eingetragenen Auftritte verglichen - mit täglich aktualisierten Zahlen.</p> 	
			<p>Unser Projekt ist überkonfessionell, d. h. egal ob evangelisch, katholisch oder freikirchlich - alle können mitmachen. 
				Unsere Ansichten bieten daher neben einer vollständigen Ansicht die Möglichkeit, nach Konfessionen oder auch nach Netzwerk zu filtern.</p>
		</article>
		
		<article id="faq">
			<h2>Häufig gestellte Fragen (FAQ)</h2>
			
			<h3>Wie trage ich (m)eine Gemeinde ein?</h3>
			<p>Für die Umsetzung der Karte sind natürlich die Adressen sowie die URLs der Webauftritten und der Social-Media-Profile notwendig. Da es sich hierbei um öffentlich verfügbare Informationen handelt, müssen diese „nur“ zusammengetragen und gepflegt werden. Dabei kann jeder mithelfen, der ein wenig Zeit und Lust mitbringt:</p>
			<ul>
				<li>Sie möchten Ihre (oder auch eine andere) Gemeinde ergänzen? <a href="add.php">Das geht über dieses Formular</a>. 
					In der <a href="map.php">Karte</a> oder der <a href="table.php">Tabelle</a> können Sie vorher nachschauen, ob die Gemeinde bereits gelistet ist.</li>
				<li>Sie haben selber einen Datensatz und möchten uns diesen zur Verfügung stellen? Dann kontaktieren Sie bitte einen der Entwickler.</li>
			</ul>

			<h3>Was ist der Unterschied zu ähnlichen Projekten?</h3>
			<ul>
				<li>Unser Projekt ist bewusst überkonfessionell ausgelegt.</li>
				<li><a href="data.php">Offene Daten</a>: Die Daten stehen in einem freien und offenen Format zur Verfügung, 
					d. h. die Daten können auch für andere Projekte verwendet werden.</li>
				<li>Die Daten werden automatisch validiert - das hilft bei der Suche nach Fehlern in den Daten. 
					Außerdem prüfen wir regelmäßig, ob unter den verlinkten URLs noch eine Seite erreichbar ist.
					So sind unsere Karte und Tabelle immer aktuell.</li>
			</ul>
			
			<h3>Sie haben eine Frage, die hier nicht beantwortet wird?</h3>
			<p>Dann wenden Sie sich doch an <a href="legal.php">die Entwickler</a> (auch bei <a href="https://twitter.com/kirchenimweb">Twitter</a>) - fragen Sie einfach.</p>
			<p>Natürlich sind auch Verbesserungsvorschläge und Anregungen sehr willkommen.</p>
		</article>
	</main>
	
	<?php displayFooter('index.php') ?>
</body>
</html>
