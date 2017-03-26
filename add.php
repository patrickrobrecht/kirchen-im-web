<?php
	// Include functions.
	include_once 'includes/functions.php';

	// Parse the data.
	$somethingSubmitted = false;
	$data = array();
	foreach (array('name', 'street', 'postalCode', 'city', 'countryCode', 'denomination', 'type', 'parentId', 'hasChildren') as $key) {
		if( isset($_POST[$key]) ) {
			$data[$key] = trim($_POST[$key]);
			$somethingSubmitted = true;
		} else {
			$data[$key] = '';
		}
	}
	
	// Check the data.
	$dataCorrect = true;
	$message = '';
	if ( !isName($data['name']) ) {
		$dataCorrect = false;
		$message .= _('Bitte einen gültigen Namen angeben!') . ' ';
	}
	if ( !isStreet($data['street']) ) {
		$dataCorrect = false;
		$message .= _('Bitte die Straße angeben!') . ' ';
	}
	if ( !isCity($data['city']) ) {
		$dataCorrect = false;
		$message .= _('Bitte einen Ort angeben!') . ' ';
	}
	if ( !isCountryCode($data['countryCode']) ) {
		$dataCorrect = false;
		$message .= _('Bitte ein Land auswählen!') . ' ';
	}
	if ( !isPostalCode($data['postalCode'], $data['countryCode']) ) {
		$dataCorrect = false;
		$message .= _('Bitte eine Postleitzahl angeben!') . ' ';
	}
	if ( !isDenomination($data['denomination']) ) {
		$dataCorrect = false;
		$message .= _('Bitte eine Konfession auswählen!') . ' ';
	}
	if ( !isType($data['type']) ) {
		$dataCorrect = false;
		$message .= _('Bitte einen Gemeindetyp auswählen!') . ' ';
	}
	if ( !isParentId($data['parentId']) ) {
		$dataCorrect = false;
		$message .= _('Bitte keine oder eine gültige nächsthöhere Ebene auswählen!') . ' ';
	}
	
	if ( $data['hasChildren'] == 'on' ) {
		$data['hasChildren'] = 1;
	} else {
		$data['hasChildren'] = 0;
	}

	// Parse URLs.
	$urls = array();
	$urlsCorrect = true;
	foreach ($websites as $website_id => $websiteName) {
		if (isset($_POST[$website_id . 'URL'])) {
			$url = trim($_POST[$website_id . 'URL']);
			if ($url != '') {
				if (isValidURL($url, $websitesStartOfURL[$website_id])) {
					$urls[$website_id] = $url;
				} else {
					// a submitted URL is invalid.
					$message .= sprintf( _('Bitte eine gültige oder keine URL für %s angeben, diese muss mit %s beginnen.'), $websites[$website_id], $websitesStartOfURL[$website_id] );
					$urlsCorrect = false;
				}
			} // else no url submitted which is fine.		
		}
	}

	$errors = true;
	if ($dataCorrect && $urlsCorrect) {
		// Write data into the database
		$id = addChurchToDatabase($data, $urls);
		$message = sprintf( _('%s wurde hinzugefügt. Vielen Dank!'), getLinkToDetailsPage($id, $data['name']) );
		$errors = false;

		// Generate new JSON/CSV export.
		export();
	}
	
	// Get parameters for pre-selection.
	if ( !isset($_POST['parentId']) && isset($_GET['parentId']) ) {
		$data['parentId'] = trim($_GET['parentId']);
	}
	if ( !isset($_POST['denomination']) && isset($_GET['denomination']) ) {
		$data['denomination'] = trim($_GET['denomination']);
	}
	if ( !isset($_POST['countryCode']) && isset($_GET['countryCode']) ) {
		$data['countryCode'] = trim($_GET['countryCode']);
	}
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Gemeinde hinzufügen'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>
		<?php echo _('Hier können Sie Ihre Gemeinde zu kirchen-im-web.de hinzufügen.'); ?>">
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Gemeinde hinzufügen'); ?></h1>
		<p><?php echo _('Hier können Sie Ihre Gemeinde zu kirchen-im-web.de hinzufügen.'); ?> 
			<strong><?php echo _('Um doppelte Eintragungen zu vermeiden, kontrollieren Sie bitte vor dem Ausfüllen, ob die Gemeinde bereits aufgeführt ist'); ?></strong>
				(<a href="map.php"><?php echo _('in der Karte'); ?></a> <?php echo _('oder'); ?> <a href="table.php"><?php echo _('in der Tabelle mit Filter-Möglichkeiten'); ?></a>).</p>
		<p><?php echo sprintf( _('In der Tabelle erscheint eine neu eingetragene Gemeinde sofort, in Karte bzw. im %s erst bis zu einen Tag später.'), '<a href="table.php?compare=true">' . _('Vergleich der Social-Media-Auftritte') . '</a> ')?></p>
		<p><output>
			<?php if ($somethingSubmitted) {
				echo $message; 
			} ?></output></p>
		<form method="post">
			<fieldset>
				<legend><?php echo _('Adresse, Konfession und Hierarchie');?></legend>
				<input id="name" name="name" type="text" required value="<?php if ($errors) echo $data['name']; ?>">
				<label for="name"><?php echo _('Name'); ?></label>
				<input id="street" name="street" type="text" required value="<?php if ($errors) echo $data['street']; ?>">
				<label for="street"><?php echo _('Straße'); ?>, <abbr title="<?php echo _('Hausnummer'); ?>"><?php echo _('Nr.'); ?></abbr></label>
				<input id="postalCode" name="postalCode" type="text" pattern="[0-9]{4,5}" required value="<?php if ($errors && $data['postalCode'] != '') echo postalCodeString($data['postalCode'], $data['countryCode']); ?>">
				<label for="postalCode"><?php echo _('PLZ'); ?></label>
				<input id="city" name="city" type="text" required value="<?php if ($errors) echo $data['city']; ?>">
				<label for="city"><?php echo _('Ort'); ?></label>
				<select id="countryCode" name="countryCode">
				<?php 
					foreach($countries as $country => $countryName) {
						showOption($country, $countryName, $country == $data['countryCode'] && $errors);
					} 
				?>
				</select>
				<label for="countryCode"><?php echo _('Land'); ?></label>
				<select id="denomination" name="denomination">
					<?php 
					foreach($denominations as $value => $denominationName) {
						showOption($value, $denominationName, $value == $data['denomination'] && $errors);
					} 
					?>
				</select>
				<label for="denomination"><?php echo _('Konfession'); ?></label>
				<select id="type" name="type">
				<?php 
					foreach($types as $value => $typeName) {
						showOption($value, $typeName, ($value == $data['type'] && $errors) || $value == $defaultType);
					}
				?>
				</select>
				<label for="type"><?php echo _('Gemeindetyp'); ?></label>
				<select id="parentId" name="parentId" class="c<?php echo $data['parentId']?>">
					<option><?php echo _('keine'); ?></option>
				<?php
					$query = 'SELECT id, name FROM churches 
						WHERE hasChildren = 1
						ORDER BY name';
					$statement = $connection->query($query);
					while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
						showOption($row['id'], $row['name'], $row['id'] == $data['parentId'] && ($errors || isset($_GET['parentId'])) );
					}
				?>
				</select>
				<label for="parentId"><?php echo _('nächsthöhere Ebene'); ?></label>
				<input id="hasChildren" name="hasChildren" type="checkbox">
				<label for="hasChildren"><?php echo _('hat untergeordnete Ebene'); ?></label>
				<p><?php echo _('Bei "Gemeindetyp" ist eine passende Auswahl zu treffen. "Pastoraler Raum" beschreibt dabei Zusammenschlüsse von mehreren katholischen Pfarreien/Pfarrvikarien (unterschiedliche Bezeichnungen in den Bistümern: Pastoralverbund, Pfarreiengemeinschaft, Seelsorgeeinheit etc.).'); ?>
				<p><?php echo _('Die nächsthöhere Ebene ist bei katholischen Pfarreien und Pfarrvikarien das Dekanat, bei Dekanaten das zugehörige Bistum bzw. bei evangelischen Kirchengemeinden entsprechend der Kirchenkreis bzw. die Landeskirche.'); ?>
					<?php echo _('Existiert noch kein Eintrag für die nächsthöhere Ebene, sollte dieser zuvor angelegt werden (hierbei ein Häkchen bei "hat übergeordnete Ebene" nicht vergessen).'); ?></p>
				<p><?php echo _('Der Haken bei "hat untergeordnete Ebene" ist zu setzen, wenn die Gemeinde selbst unter "nächsthöhere Ebene" aufgeführt werden soll (also bei Dekanate und bei Kirchenkreisen). Bei Kirchengemeinden darf der Haken nicht gesetzt werden!'); ?></p>
			</fieldset>
			<fieldset>
				<legend><?php echo _('URLs des Webauftritts und der Social-Media-Profile'); ?></legend>
				<p><?php echo _('Bitte achten Sie darauf, nur öffentliche Webauftritte und Social-Media-Auftritte (d.h. keine Facebook-Gruppen, sondern nur öffentliche Facebook-Seiten) anzugeben.'); ?></p>
				<p><?php echo _('Wenn eine Seite verschlüsselt (also mit https) erreichbar ist, wählen Sie bitte die URL mit https.'); ?></p>
				<?php 
					foreach ($websites as $website_id => $website) {
				?>
				<input id="<?php echo $website_id; ?>URL" name="<?php echo $website_id; ?>URL" type="url" oninput="checkURL(this)" value="<?php if ($errors && array_key_exists($website_id, $urls)) echo $urls[$website_id]; ?>">
				<label class="<?php echo $website_id; ?>" for="<?php echo $website_id; ?>URL"><?php echo $website; ?></label>
				<?php 
					}
				?>
				<button type="submit"><?php echo _('Gemeinde hinzufügen'); ?></button>
				<p><strong><?php echo _('Bitte achten Sie darauf, dieses Formular nur einmal mit denselben Daten abzuschicken'); ?></strong> 
					(<?php echo _('sonst erzeugen Sie mehrere identische Einträge'); ?>).
					<?php echo _('Bei Fehlern in Einträgen wenden Sie sich bitte an '); ?> kontakt [ät] kirchen-im-web [punkt] de.</p>
			</fieldset>
		</form>
	</main>
	<script type="text/javascript" src="/js/check.js"></script>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>