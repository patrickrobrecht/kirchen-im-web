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
<html lang="de-DE">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Gemeinde hinzufügen</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte. Hier können Sie Ihre Gemeinde zu kirchen-im-web.de hinzufügen.">
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php displayHeader('add.php'); ?>
	
	<main>
		<h1>Gemeinde hinzufügen</h1>
		<p>Mit diesem Formular können Sie Ihre oder andere Gemeinden diesem Verzeichnis hinzufügen. 
			<strong>Um doppelte Eintragungen zu vermeiden, kontrollieren Sie bitte vor dem Ausfüllen, ob die Gemeinde bereits aufgeführt ist (<a href="map.php">in der Karte</a> oder <a href="table.php">in der Tabelle</a>) mit Filter-Möglichkeiten.</strong></p>
		<p>In der Tabelle erscheint eine neu eingetragene Gemeinde sofort, in Karte bzw. <a href="table.php?compare=true">Social-Media-Vergleich</a> erst bis zu einen Tag später.</p>
		<p><output>
			<?php if ($somethingSubmitted) {
				echo $message; 
			} ?></output></p>
		<form method="post">
			<fieldset>
				<legend>Name, Adresse, Konfession</legend>
				<input id="name" name="name" type="text" required value="<?php if ($errors) echo $data['name']; ?>">
				<label for="name">Name</label>
				<input id="street" name="street" type="text" required value="<?php if ($errors) echo $data['street']; ?>">
				<label for="street">Straße, <abbr title="Hausnummer">Nr.</abbr></label>
				<input id="postalCode" name="postalCode" type="text" pattern="[0-9]{4,5}" required value="<?php if ($errors) echo postalCodeString($data['postalCode'], $data['countryCode']); ?>">
				<label for="postalCode"><abbr title="Postleitzahl">PLZ</abbr></label>
				<input id="city" name="city" type="text" required value="<?php if ($errors) echo $data['city']; ?>">
				<label for="city">Ort</label>
				<select id="countryCode" name="countryCode">
				<?php 
					foreach($countries as $country => $countryName) {
						showOption($country, $countryName, $country == $data['countryCode'] && $errors);
					} 
				?>
				</select>
				<label for="countryCode">Land</label>
				<select id="denomination" name="denomination">
					<?php 
					foreach($denominations as $value) {
						showOption($value, $value, $value == $data['denomination'] && $errors);
					} 
					?>
				</select>
				<label for="denomination">Konfession</label>
				<select id="type" name="type">
				<?php 
					foreach($types as $value) {
						showOption($value, $value, ($value == $data['type'] && $errors) || $value == $defaultType);
					}
				?>
				</select>
				<label for="type">Gemeindetyp</label>
				<select id="parentId" name="parentId" class="c<?php echo $data['parentId']?>">
					<option>keine</option>
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
				<label for="parent">gehört zu</label>
				<input id="hasChildren" name="hasChildren" type="checkbox">
				<label for="hasChildren">hat untergeordnete Ebene</label>
				<p>Bei "Gemeindetyp" ist eine passende Auswahl zu treffen. "Pastoraler Raum" beschreibt dabei Zusammenschlüsse von mehreren katholischen Pfarreien/Pfarrvikarien (unterschiedliche Bezeichnungen in den Bistümern: Pastoralverbund, Pfarreiengemeinschaft, Seelsorgeeinheit etc.).
				<p>Unter "gehört zu" ist die nächsthöhere Ebene - bei katholischen Pfarreien und Pfarrvikarien das Dekanat, bei Dekanaten das zugehörige Bistum bzw. bei evangelischen Kirchengemeinden entsprechend der Kirchenkreis bzw. die Landeskirche - anzugeben. Existiert noch kein Eintrag für die nächsthöhere Ebene, sollte dieser zuvor angelegt werden (hierbei ein Häkchen bei "hat übergeordnete Ebenen" nicht vergessen).</p>
				<p>Der Haken bei "hat untergeordnete Ebene" ist zu setzen, wenn die Gemeinde selbst unter "gehört zu" aufgeführt werden soll (also bei Dekanate und bei Kirchenkreisen). Bei Kirchengemeinden darf der Haken nicht gesetzt werden!</p>
			</fieldset>
			<fieldset>
				<legend>URLs der Webseite und der Social-Media-Profile</legend>
				<p>Bitte achten Sie darauf, nur öffentliche Webauftritte und Social-Media-Auftritte (d.h. keine Facebook-Gruppen, sondern nur öffentliche Facebook-Seiten) anzugeben.</p>		
				<p>Wenn eine Seite verschlüsselt (also mit https) erreichbar ist, wählen Sie bitte die URL mit https.</p>
				<?php 
					foreach ($websites as $website_id => $website) {
				?>
				<input id="<?php echo $website_id; ?>URL" name="<?php echo $website_id; ?>URL" type="url" oninput="checkURL(this)" value="<?php if ($errors && array_key_exists($website_id, $urls)) echo $urls[$website_id]; ?>">
				<label for="<?php echo $website_id; ?>URL"><?php echo $website; ?></label>
				<?php 
					}
				?>
				<button type="submit">Gemeinde hinzufügen</button>
				<p><strong>Bitte achten Sie darauf, dieses Formular nur einmal mit denselben Daten abzuschicken</strong> (sonst erzeugen Sie mehrere identische Einträge).
					Bei Fehlern in Einträgen wenden Sie sich bitte an kontakt [ät] kirchen-im-web [punkt] de.</p>
			</fieldset>
		</form>
	</main>

	<script type="text/javascript" src="js/check.js"></script>
	
	<?php displayFooter('add.php') ?>
</body>
</html>
