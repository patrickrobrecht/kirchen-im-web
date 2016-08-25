<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Linkliste Webauftritte und Social-Media-Auftritte gestalten</title>
	<meta name="description" content="Hier gibt">
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	
	<main>
		<h1>Tipps und Tricks</h1>
		<p>Diese Unterseite ist noch im Aufbau! Vorschläge für Links zu weiteren hilfreichen Onlineressourcen sind immer willkommen!</p>
		
		<nav>
			<h2>Inhalt</h2>
			<ul>
				<li><a href="#webauftritte">Kirchliche Webauftritte</a></li>
				<li><a href="#social-media">Kirche und Social Media</a>
					<ul>
						<li><a href="#facebook">Facebook</a></li>
						<li><a href="#twitter">Twitter</a></li>
					</ul></li>
				<li><a href="#rechtliches">Rechtliches</a></li>
				<li><a href="#oefflichkeitsarbeit">Kirchliche Öffentlichkeitsarbeit allgemein</a></li>
				<li><a href="#veranstaltungen">Veranstaltungen</a></li>
				<li><a href="#weitere">Weitere Links</a></li>
			</ul>
		</nav>
		
		<article id="webauftritte">
			<h2>Kirchliche Webauftritte</h2>
			<ul>
				<li><a href="https://de.wordpress.org/">WordPress</a> - kostenloses OpenSource Content Management System, das über sog. Themes in Layout und Design angepasst werden kann. Über Plugins können viele weitere Funktionen installiert werden.
					<ul>
						<li><a href="https://de.wordpress.org/themes/">Themes</a> - kostenlose Design-/Layouts für WordPress</li>
						<li><a href="https://de.wordpress.org/plugins/polaroid-gallery/">Bildergalerien mit Slideshow</a></li>
						<li><a href="https://de.wordpress.org/plugins/statify/">datenschutzfreundliche Aufrufstatistiken</a> und <a href="https://de.wordpress.org/plugins/extended-evaluation-for-statify/">eine ausführlichere Auswertung</a></li>
						<li><a href="https://de.wordpress.org/plugins/wysija-newsletters/">Newsletter</a> mit mehreren Listen, die über das WordPress-Backend verschickt werden können.</li>
						<li><a href="https://de.wordpress.org/plugins/seo-ultimate/">Suchmaschinenoptimierung</a> und <a href="https://wordpress.org/plugins/google-sitemap-generator/">XML-Sitemap für Suchmaschinen</a></li>
						<li><a href="https://de.wordpress.org/plugins/events-manager/">Veranstaltungskalender</a></li>
						<li><a href="https://www.elegantthemes.com/blog/tips-tricks/how-to-build-a-church-website-with-wordpress" hreflang="en">How to build a church website with WordPress</a> (in English)</li>
					</ul>						
			</ul>
		</article>
		
		<article id="social-media">
			<h2>Kirche und Social Media</h2>
			<ul>
				<li><a href="http://www.smg-rwl.de/">Social Media Guidelines</a> der Evangelischen Kirche im Rheinland, der Evangelischen Kirche von Westfalen oder der Lippischen Landeskirche</li>
				<li><a href="http://www.caritas-digital.de/so-will-die-caritas-online-kommunizieren/">So wollen wir online kommunizieren - Richtlinien der Caritas</a></li>
			</ul>
			<section id="facebook">
				<h3>Facebook</h3>
				<ul>
					<li><a href="table.php?compare=true&sort=facebook">Positiv-Beispiele auf kirchen-im-web.de</a></li>
				</ul>
			</section>
			<section id="twitter">
				<h3>Twitter</h3>
				<ul>
					<li><a href="table.php?compare=true&sort=twitter">Positiv-Beispiele auf kirchen-im-web.de</a></li>
				</ul>
			</section>
		</article>
		
		<article id="rechtliches">
			<h2>Rechtliches</h2>
			<ul>
				<li><a href="http://nordbild.com/kirchliche-presse-fotos/">Kirchliche Presse und die Personenfotos</a> – Was ist auf Tagungen erlaubt | <a href="http://nordbild.com/bildrechte-check/">Checkliste Bildrechte</a></li>
				<li><a href="http://www.pfarrbriefservice.de/faq/kostenlose-bilder-aus-dem-internet">Kostenlose Bilder aus dem Internet</a> - Was Sie beachten sollten</li>
				<li><a href="http://www.pfarrbriefservice.de/page/antworten-auf-rechtliche-fragen-rund-um-die-pfarrbriefarbeit">Antworten auf rechtliche Fragen rund um die Pfarrbriefarbeit</a></li>
				<li><a href="https://rechtsbelehrung.com/hatespeech-rechtsbelehrung-folge-35-jura-podcast/">rechtliche Aspekte von Hatespeech</a></li>
			</ul>
		</article>
		
		<article id="oefflichkeitsarbeit">
			<h2>Kirchliche Öffentlichkeitsarbeit allgemein</h2>
			<ul>
				<li><a href="http://www.pfarrbriefservice.de/">Pfarrbriefservice</a> (katholisch) | <a href="http://www.pfarrbriefservice.de/page/kurs-pfarrbrief">Online-Kurs Pfarrbrief</a></li>
			</ul>
		</article>
		
		<article id="veranstaltungen">
			<h2>Veranstaltungen</h2>
			<ul>
				<li><a href="http://barcamp-kirche-online.de/">Barcamp Kirche online</a>, 23.-25. September 2016, Köln</li>
				<li><a href="http://www.pfarrbriefservice.de/workshop/events">Veranstaltungen von pfarrbriefservice.de</a></li>
			</ul>
		</article>		
		
		<article id="weitere">
			<h2>Weitere interessante Links</h2>
			<ul>
				<li><a href="http://medienkompetenz.katholisch.de/">Clearingstelle Medienkompetenz</a></li>
				<li><a href="https://luki.org/">Linux User im Bereich der Kirchen e. V.</a></li>
			</ul>
		</article>
	</main>
	
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>
