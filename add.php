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
	
	if ( !isset($_POST['parentId']) && isset($_GET['parentId']) ) {
		$data['parentId'] = trim($_GET['parentId']);
	}
	
	// Check the data.
	$dataCorrect = true;
	$message = '';
	if ( !isName($data['name']) ) {
		$dataCorrect = false;
		$message .= 'Bitte einen gültigen Namen angeben! ';
	}
	if ( !isStreet($data['street']) ) {
		$dataCorrect = false;
		$message .= 'Bitte die Straße angeben! ';
	}
	if ( !isCity($data['city']) ) {
		$dataCorrect = false;
		$message .= 'Bitte einen Ort angeben! ';
	}
	if ( !isCountryCode($data['countryCode']) ) {
		$dataCorrect = false;
		$message .= 'Bitte ein Land auswählen! ';
	}
	if ( !isPostalCode($data['postalCode'], $data['countryCode']) ) {
		$dataCorrect = false;
		$message .= 'Bitte eine Postleitzahl angeben! ';
	}
	if ( !isDenomination($data['denomination']) ) {
		$dataCorrect = false;
		$message .= 'Bitte eine Konfession angeben! ';
	}
	if ( !isType($data['type']) ) {
		$dataCorrect = false;
		$message .= 'Bitte einen Gemeindetyp auswählen! ';
	}
	if ( !isParentId($data['parentId']) ) {
		$dataCorrect = false;
		$message .= 'Bitte keine oder eine gültige nächsthöhere Ebene auswählen! ';
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
					$message .= 'Bitte eine gültige oder keine URL für ' . $websites[$website_id] . ' angeben, diese muss mit ' . $websitesStartOfURL[$website_id] . ' beginnen.';
					$urlsCorrect = false;
				}
			} // else no url submitted which is fine.		
		}
	}

	$errors = true;
	if ($dataCorrect && $urlsCorrect) {
		// Write data into the database
		$id = addChurchToDatabase($data, $urls);
		$message = getLinkToDetailsPage($id, $data['name']) . ' wurde hinzugefügt. Vielen Dank!';
		$errors = false;
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
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Gemeinde hinzufügen'); ?></h1>
		<p><?php echo _('Hier können Sie Ihre Gemeinde zu kirchen-im-web.de hinzufügen.'); ?> 
			<strong><?php echo _('Um doppelte Eintragungen zu vermeiden, kontrollieren Sie bitte vor dem Ausfüllen, ob die Gemeinde bereits aufgeführt ist'); ?> 
				(<a href="map.php"><?php echo _('in der Karte'); ?></a> <?php echo _('oder'); ?> <a href="table.php"><?php echo _('in der Tabelle mit Filter-Möglichkeiten'); ?></a>).</strong></p>
		<p><?php echo sprintf( _('In der Tabelle erscheint eine neu eingetragene Gemeinde sofort, in Karte bzw. im %s erst bis zu einen Tag später.'), '<a href="table.php?compare=true">' . _('Social-Media-Vergleich') . '</a> ')?></p>
		<p><output>
			<?php if ($somethingSubmitted) {
				echo $message; 
			} ?></output></p>
		<form method="post">
			<fieldset>
				<legend><?php echo _('Name') . ', ' . _('Adresse') . ', ' . _('Konfession');?></legend>
				<input id="name" name="name" type="text" required value="<?php if ($errors) echo $data['name']; ?>">
				<label for="name"><?php echo _('Name'); ?></label>
				<input id="street" name="street" type="text" required value="<?php if ($errors) echo $data['street']; ?>">
				<label for="street"><?php echo _('Straße'); ?>, <abbr title="<?php echo _('Hausnummer'); ?>"><?php echo _('Nr.'); ?></abbr></label>
				<input id="postalCode" name="postalCode" type="text" pattern="[0-9]{4,5}" required value="<?php if ($errors) echo postalCodeString($data['postalCode'], $data['countryCode']); ?>">
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
					foreach($denominations as $value) {
						showOption($value, $value, $value == $data['denomination'] && $errors);
					} 
					?>
				</select>
				<label for="denomination"><?php echo _('Konfession'); ?></label>
				<select id="type" name="type">
				<?php 
					foreach($types as $value) {
						showOption($value, $value, ($value == $data['type'] && $errors) || $value == $defaultType);
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
				<label for="parent"><?php echo _('gehört zu'); ?></label>
				<input id="hasChildren" name="hasChildren" type="checkbox">
				<label for="hasChildren"><?php echo _('hat untergeordnete Ebene'); ?></label>
				<p><?php echo _('Bei "Gemeindetyp" ist eine passende Auswahl zu treffen. "Pastoraler Raum" beschreibt dabei Zusammenschlüsse von mehreren katholischen Pfarreien/Pfarrvikarien (unterschiedliche Bezeichnungen in den Bistümern: Pastoralverbund, Pfarreiengemeinschaft, Seelsorgeeinheit etc.).'); ?>
				<p><?php echo _('Unter "gehört zu" ist die nächsthöhere Ebene - bei katholischen Pfarreien und Pfarrvikarien das Dekanat, bei Dekanaten das zugehörige Bistum bzw. bei evangelischen Kirchengemeinden entsprechend der Kirchenkreis bzw. die Landeskirche - anzugeben.');
						echo _('Existiert noch kein Eintrag für die nächsthöhere Ebene, sollte dieser zuvor angelegt werden (hierbei ein Häkchen bei "hat übergeordnete Ebenen" nicht vergessen).'); ?></p>
				<p><?php echo _('Der Haken bei "hat untergeordnete Ebene" ist zu setzen, wenn die Gemeinde selbst unter "gehört zu" aufgeführt werden soll (also bei Dekanate und bei Kirchenkreisen). Bei Kirchengemeinden darf der Haken nicht gesetzt werden!'); ?></p>
			</fieldset>
			<fieldset>
				<legend><?php echo _('URLs des Webauftritts und der Social-Media-Profile'); ?></legend>
				<p><?php echo _('Bitte achten Sie darauf, nur öffentliche Webauftritte und Social-Media-Auftritte (d.h. keine Facebook-Gruppen, sondern nur öffentliche Facebook-Seiten) anzugeben.'); ?></p>
				<p><?php echo _('Wenn eine Seite verschlüsselt (also mit https) erreichbar ist, wählen Sie bitte die URL mit https.'); ?></p>
				<?php 
					foreach ($websites as $website_id => $website) {
				?>
				<input id="<?php echo $website_id; ?>URL" name="<?php echo $website_id; ?>URL" type="url" oninput="checkURL(this)" value="<?php if ($errors && array_key_exists($website_id, $urls)) echo $urls[$website_id]; ?>">
				<label for="<?php echo $website_id; ?>URL"><?php echo $website; ?></label>
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
	<script type="text/javascript" src="js/check.js"></script>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>