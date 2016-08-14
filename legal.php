<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Impressum'); ?> - kirchen-im-web.de</title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Impressum'); ?></h1>
	
		<p><?php echo _('kirchen-im-web.de ist ein Gemeinschaftsprojekt von'); ?></p>
		<ul>
			<li><a href="http://joerg-lohrer.de/">Jörg Lohrer</a></li>
			<li><a href="https://patrick-robrecht.de/">Patrick Robrecht</a></li>
		</ul>
		
		<p><?php echo _('Idee'); ?>: Jörg Lohrer</p>
		<p><?php echo _('Technische Umsetzung'); ?>: Patrick Robrecht</p>
		<p><?php echo _('Daten: viele Leute, die Daten über unser <a href="add.php">Formular</a> hinzugefügt haben'); ?>
			<br><?php echo _('Für die Korrektur falscher Daten wenden Sie sich bitte an Patrick Robrecht:'); ?>
			kontakt [ät-Zeichen] kirchen [minus] im [minus] web de.</p>
		
		<article>
			<h2><?php _('Lizenzen'); ?></h2>
			<p><?php _('Social Media Icons'); ?>: CC BY-NC-ND 3.0, <a href="http://www.designbolts.com/2013/06/27/new-flat-free-social-media-icons-2013/">DesignBolts.com</a>
		</article>
				
		<article>
			<h2><?php echo _('Impressum'); ?></h2>
			<p>Verantwortlich für diesen Webauftritt ist: 
				<br>Patrick Robrecht, Dr.-Rörig-Damm 99, 33102 Paderborn
				<br>E-Mail: kontakt [ät-Zeichen] kirchen [minus] im [minus] web de
				<br>Twitter: <a href="https://twitter.com/kirchenimweb/">@kirchenimweb</a></p>
		</article>
	</main>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>