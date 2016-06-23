<?php 
	include_once 'includes/functions.php';
	
	// Get the values for the filters.
	$name = isset($_GET['name']) ? trim($_GET['name']) : '';
	$postalCode = isset($_GET['postalCode']) ? intval($_GET['postalCode']) : '';
	$city = isset($_GET['city']) ? trim($_GET['city']) : '';
	$country = isset($_GET['countryCode']) ? trim($_GET['countryCode']) : '';
	$denomination = isset($_GET['denomination']) ? trim($_GET['denomination']) : '';
	$type = isset($_GET['type']) ? trim($_GET['type']) : '';
	$hasWebsiteType = isset($_GET['hasWebsiteType']) ? trim($_GET['hasWebsiteType']) : '';
	
	$compare = isset($_GET['compare']) ? ($_GET['compare'] == 'true') : false;
	
	// Get the website types to be shown.
	if ($compare) {
		$showWebsites = $networksToCompare;
	} else {
		$showWebsites = array();
		foreach($websites as $websiteId => $websiteName) {
			if ($hasWebsiteType == $websiteId
					|| (isset($_GET[$websiteId]) && $_GET[$websiteId] == 'show') ) {
				$showWebsites[$websiteId] = $websiteName;
			}
		}
	}
	// If no website type is selected, use default.
	if (sizeof($showWebsites) == 0) {
		$showWebsites = $preselected;
	}
?>
<!DOCTYPE html>
<html lang="de-DE">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<?php if ($compare) { ?>
	<title>Vergleich kirchlicher Social-Media-Auftritte</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte. Hier werden viele Profile deutschsprachiger Gemeinden verglichen.">
	<?php } else { ?>
	<title>Tabelle: Kirchliche Web- und Social-Media-Auftritte (tabellarisch)</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte. Hier eine tabellarische, überkonfessionelle Übersicht für den deutsprachigen Raum.">
	<?php } ?>	
	<link rel="stylesheet" href="./css/style.css">
	<link rel="stylesheet" href="./css/theme.default.css">
</head>
<body>
	<?php if ($compare) {
		displayHeader('table.php?compare=true'); 
	} else {
		displayHeader('table.php');
	} ?>
	
	<main>
		<?php if ($compare) {?>
		<h1>Vergleich kirchlicher Social-Media-Auftritte</h1>
		<p>Hier gibt's einen Vergleich der Like-Zahlen für Facebook-Seiten, der Follower bei Twitter sowie der Abonnenten der YouTube-Kanäle. Mit einem Klick auf die jeweilige Spalte kann man nach dieser sortieren.</p>
		<p>Wenn für einen Social-Media-Auftritt kein Ergebnis ermittelt werden konnte (z. B. weil die Facebook-Seite nicht öffentlich zugänglich ist), wird dieser in diesem Vergleich nicht aufgeführt.</p>
		<?php } else { ?>
		<h1>Kirchliche Web- und Social-Media-Auftritte</h1>
		<?php } ?>
		<form method="get">
			<input type="hidden" name="compare" id="compare" value="<?php if ($compare) echo 'true'; else echo 'false'; ?>">
			<fieldset>
				<legend>Filter</legend>
				<input id="name" name="name" type="text" value="<?php echo $name; ?>">
				<label for="name">Name</label>
				<input id="postalCode" name="postalCode" type="text" pattern="[0-9]{4,5}" value="<?php if ($postalCode != 0) echo postalCodeString($postalCode, $country); ?>">
				<label for="postalCode"><abbr title="Postleitzahl">PLZ</abbr></label>
				<input id="city" name="city" type="text" value="<?php echo $city; ?>">
				<label for="city">Ort</label>
				<select id="countryCode" name="countryCode">
					<option></option>
				<?php 
					foreach($countries as $countryCode => $countryName) {
						showOption($countryCode, $countryName, $countryCode == $country);
					} 
				?>
				</select>
				<label for="countryCode">Land</label>
				<select id="denomination" name="denomination">
					<option></option>
					<?php 
					foreach($denominations as $value) {
						showOption($value, $value, $value == $denomination);
					} 
					?>
				</select>
				<label for="denomination">Konfession</label>
				<select id="type" name="type">
					<option></option>
				<?php 
					foreach($types as $value) {
						showOption($value, $value, $value == $type);
					}
				?>
				</select>
				<label for="type">Gemeindetyp</label>
				<select id="hasWebsiteType" name="hasWebsiteType">
					<option></option>
				<?php
					foreach($websites as $websiteId => $websiteName) {
						showOption($websiteId, $websiteName, $websiteId == $hasWebsiteType);
					} 
				?>
				</select>
				<label for="hasWebsiteType">Nur Gemeinden mit</label>
			</fieldset>
			<fieldset style="width:10em;">
				<?php if (!$compare) {?>
				<legend>Anzeige</legend>
				<?php
					foreach($websites as $websiteId => $websiteName) {
				?>
					<div>
						<input id="<?php echo $websiteId; ?>" name="<?php echo $websiteId; ?>" type="checkbox"
							value="show"<?php if (array_key_exists($websiteId, $showWebsites)) echo " checked"; ?>>
						<label for="<?php echo $websiteId; ?>"><?php echo $websiteName; ?></label>
					</div>
				<?php
					}
				}
				?>
				<button type="submit">Filtern</button>
			</fieldset>
		</form>
		
		<table id="churchTable" class="tablesorter">
			<thead>
				<tr>
					<th>Name</th>
					<th>PLZ</th>
					<th>Ort</th>
					<th>Land</th>
					<th>Konfession</th>
					<th>Typ</th>					
				<?php 
					foreach ($showWebsites as $websiteName) {
				?>
					<th><?php echo $websiteName; ?></th>
				<?php 
					}
				?>
				</tr>
			</thead>
			<tbody>
			<?php
				// Query
				$query = 'SELECT id, name, postalCode, city, country, denomination, churches.type';
				foreach ($showWebsites as $websiteId => $websiteName) {
					$query .= ', ' .$websiteId . '.url AS ' . $websiteId . ', ' . $websiteId . '.followers AS ' . $websiteId . '_followers';
				}
				$query .= ' FROM churches ';
			
				// .... get the URLs to show.
				foreach ($showWebsites as $websiteId => $websiteName) {
					$query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.cid = churches.id
								AND ' . $websiteId . '.type = "' . $websiteId . '" ';
				}

				// ... and apply the filters
				$conditions = array();
				if ($name != '') {
					array_push($conditions, 'name LIKE :name ');
				}
				if ($postalCode != 0) {
					array_push($conditions, 'postalCode = :postalCode ');
				}
				if ($city != '') {
					array_push($conditions, 'city LIKE :city ');
				}
				if ($country != '') {
					array_push($conditions, 'country = :country ');
				}
				if ($denomination != '') {
					array_push($conditions, 'denomination = :denomination ');
				}
				if ($type != '') {
					array_push($conditions, 'churches.type = :ctype ');
				}
				if ($hasWebsiteType != '') {
					array_push($conditions, 'EXISTS (SELECT * FROM websites WHERE id = cid AND type = :wtype) ');
				}
				if ($compare) {
					// restrict to churches with at least one profile with followers set
					$compare_conditions = array();
					foreach ($networksToCompare as $websiteId => $websiteName) {
						array_push($compare_conditions, '(' . $websiteId . '.followers IS NOT NULL AND ' . $websiteId . '.followers > 0) ');
					}
					if (sizeof($compare_conditions) > 0) {
						$only_socialmedia_compare_condition = '';
						foreach ($compare_conditions as $condition) {
							$only_socialmedia_compare_condition .= ' OR ' . $condition;
						}
						$only_socialmedia_compare_condition = preg_replace('/ OR/', '(', $only_socialmedia_compare_condition, 1) . ')';
					}
					array_push($conditions, $only_socialmedia_compare_condition);
				}
				
				if (sizeof($conditions) > 0) {
					$whereclause = '';
					foreach ($conditions as $condition) {
						$whereclause .= ' AND ' . $condition;
					}
					$whereclause = preg_replace('/ AND/', ' WHERE', $whereclause, 1);
					$query .= $whereclause;
					$query .= 'ORDER BY country, postalCode';
				} else {
					$query .= 'ORDER BY churches.timestamp DESC, id DESC LIMIT 25';
				}
				
				$statement = $connection->prepare($query);
				if ($name != '') {
					$name = '%' . $name . '%';
					$statement->bindParam(':name', $name);
				}
				if ($postalCode != 0) {
					$statement->bindParam(':postalCode', $postalCode);
				}
				if ($city != '') {
					$city = '%' . $city . '%';
					$statement->bindParam(':city', $city);
				}
				if ($country != '') {
					$statement->bindParam(':country', $country);
				}
				if ($denomination != '') {
					$statement->bindParam(':denomination', $denomination);
				}
				if ($type != '') {
					$statement->bindParam(':ctype', $type);
				}
				if ($hasWebsiteType != '') {
					$statement->bindParam(':wtype', $hasWebsiteType);
				}
				
				$statement->execute();
				
				$counter = 0;
				while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
					$counter++;
			?>
				<tr>
					<td><?php echo getLinkToDetailsPage($row['id'], $row['name']); ?></td>
					<td><?php echo postalCodeString($row['postalCode'], $row['country']); ?></td>
					<td><?php echo $row['city']; ?></td>
					<td><?php echo $countries[$row['country']]; ?></td>
					<td><?php echo $row['denomination']; ?></td>
					<td><?php echo $row['type']; ?></td>
				<?php
					foreach ($showWebsites as $websiteId => $websiteName) { 
						$url = $row[$websiteId];
						$followers = $row[$websiteId . '_followers'];
						if ($url == '') {
				?>
					<td></td>
				<?php 	
						} else {
							if ($followers) {
				?>
					<td class="number"><a href="<?php echo $url; ?>"><?php echo GermanNumberFormat($followers); ?></td>
				<?php 		
							} else { 
				?>
					<td><?php if (!$compare) {?><a href="<?php echo $url; ?>"><?php echo $websiteName; } ?></td>
				<?php 		
							}
						}
					} 
				?>
				</tr>
				<?php
				} // end while
			?>
			</tbody>
		</table>
		<p><output>
		<?php if ($counter == 0) {
			echo 'Leider keine Gemeinde ';
		} else if ($counter == 1) {
			echo 'Eine Gemeinde ';
		} else {
			echo $counter . ' Gemeinden ';
		} ?>
			gefunden!</output></p>
	</main>
	
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.tablesorter.js"></script>
	<script>$("#churchTable").tablesorter({
<?php 
		if ($compare) {
			$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
			$sortColumnId = 0;
			if (array_key_exists($sort, $networksToCompare)) {
				$sortColumnId = 6 + array_search($sort, array_keys($networksToCompare));
			}
			if ($sortColumnId == 0) {
?>
		sortList: [ [6,1], [7,1], [8,1] ],
<?php 
			} else {
?>
		sortList: [ [<?php echo $sortColumnId; ?>,1] ],
<?php 
			}
		} else {
?>
		sortList: [ [3,0] ],
<?php 
		} 
?>
		usNumberFormat: false,
	});</script>
	
	<?php displayFooter('table.php') ?>
</body>
</html>