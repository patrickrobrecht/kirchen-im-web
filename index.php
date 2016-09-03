<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Das Projekt kirchen-im-web.de'); ?></h1>

		<article id="info">
			<h2><?php echo _('Über das Projekt'); ?></h2>
			<figure>
				<a href="map.php"><img alt="<?php echo _('Karte'); ?>" src="/images/screenshot.png"></a>
				<figcaption><?php echo _('Alt-katholische Kirchen werden orange, anglikanische grün, evangelische lila, freikirchliche blau, katholische gelb und andere/ökumenische rot dargestellt.'); ?></figcaption>
			</figure>
			<p><?php echo sprintf( _('Wir wollen zeigen, dass viele Kirchengemeinden, Landeskirchen und Bistümer auch im Web gute Öffentlichtkeitsarbeit machen und stellen diese mit ihren Web- und Social-Media-Auftritten %s und %s dar.'), 
			 			'<a href="map.php">' . _('in einer Karte') . '</a>', '<a href="table.php">' . _('tabellarisch') . '</a>' ); ?>
				<?php echo sprintf( _('kirchen-im-web.de vergleicht auch die Follower-Zahlen der %s.'),
						'<a href="table.php?compare=true">' . _('Social-Media-Auftritte') . '</a>' ); ?></p> 	
			<p><?php echo _('Unser Projekt ist überkonfessionell, d. h. egal ob evangelisch, katholisch oder freikirchlich - alle können mitmachen.'); ?>
				<?php echo sprintf( _('Sie können Ihre Gemeinde einfach über unser %s hinzufügen.'), '<a href="add.php">' . _('Formular') . '</a>' ); ?></p>

			<h3><?php echo _('Wie trage ich (m)eine Gemeinde ein?'); ?></h3>
			<p><?php echo _('Für das Verzeichnis benötigen wir die Adressen sowie die Webauftritte und Social-Media-Profile.'); ?>
				<?php echo _('Da es sich hierbei um öffentlich verfügbare Informationen handelt, müssen diese „nur“ zusammengetragen und gepflegt werden.'); ?>
				<?php echo _('Dabei kann jeder mithelfen, der ein wenig Zeit und Lust mitbringt:'); ?></p>
			<ul>
				<li><?php echo _('Sie möchten Ihre (oder auch eine andere) Gemeinde ergänzen?'); ?> <a href="add.php"><?php echo _('Das geht über dieses Formular.'); ?></a>
					<?php echo _('In der Karte oder der Tabelle können Sie vorher nachschauen, ob die Gemeinde bereits gelistet ist.'); ?></li>
				<li><?php echo _('Sie haben selber einen Datensatz und möchten uns diesen zur Verfügung stellen? Dann kontaktieren Sie bitte einen der Entwickler.'); ?></li>
			</ul>

			<h3><?php echo _('Was ist der Unterschied zu ähnlichen Projekten?'); ?></h3>
			<ul>
				<li><?php echo _('Unser Projekt ist überkonfessionell, d. h. egal ob evangelisch, katholisch oder freikirchlich - alle können mitmachen.'); ?></li>
				<li><a href="data.php"><?php echo _('Offene Daten'); ?></a>:
					<?php echo _('Die Daten stehen in einem freien und offenen Format zur Verfügung, d. h. die Daten können auch für andere Projekte verwendet werden.'); ?></li>
				<li><?php echo _('Die Daten werden automatisch validiert - das hilft, Fehler zu finden.'); ?> 
					<?php echo _('Außerdem prüfen wir regelmäßig, ob die verlinkten Seiten noch existieren.'); ?>
					<?php echo _('So sind unsere Karte und Tabelle immer aktuell.'); ?></li>
				<li><?php echo sprintf( _('Auch der Quelltext dieser Webanwendung ist %s.'), '<a href="development.php">' . _('frei verfügbar') . '</a>' ); ?></li>
			</ul>
			
			<h3><?php echo _('Sie haben eine Frage, die hier nicht beantwortet wird?'); ?></h3>
			<p><a href="legal.php"><?php echo _('Dann schreiben Sie uns!'); ?></a>
				<?php echo _('Natürlich sind Verbesserungsvorschläge sehr willkommen.'); ?></p>
		</article>
	</main>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>