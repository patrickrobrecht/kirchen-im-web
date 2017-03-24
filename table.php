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
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<?php if ($compare) { ?>
	<title><?php echo _('Vergleich kirchlicher Social-Media-Auftritte');?></title>
	<meta name="description" content="<?php echo _('kirchen-im-web.de vergleicht kirchliche Social-Media-Auftritte anhand ihrer Follower-Zahlen.'); ?>">
	<?php } else { ?>
	<title><?php echo _('Tabelle'); ?>: <?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<?php } ?>	
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<?php if ($compare) {?>
		<h1><?php echo _('Vergleich kirchlicher Social-Media-Auftritte'); ?></h1>
		<p><?php echo _('Die Like-Zahlen für Facebook-Seiten, der Follower bei Twitter und Google+ sowie der Abonnenten der YouTube-Kanäle werden hier verglichen.'); ?>
			<?php echo _('Mit einem Klick auf die jeweilige Spalte kann man nach dieser sortieren.'); ?></p>
		<p><?php echo _('Wenn für einen Social-Media-Auftritt keine Zahl ermittelt werden konnte (z. B. weil die Facebook-Seite nicht öffentlich zugänglich ist), wird dieser in diesem Vergleich nicht aufgeführt.'); ?></p>
		<?php } else { ?>
		<h1><?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></h1>
		<?php } ?>
		<form method="get">
			<input type="hidden" name="compare" id="compare" value="<?php if ($compare) echo 'true'; else echo 'false'; ?>">
			<fieldset>
				<legend><?php echo _('Filter'); ?></legend>
				<input id="name" name="name" type="text" value="<?php echo $name; ?>">
				<label for="name"><?php echo _('Name'); ?></label>
				<input id="postalCode" name="postalCode" type="text" pattern="[0-9]{4,5}" value="<?php if ($postalCode != 0) echo postalCodeString($postalCode, $country); ?>">
				<label for="postalCode"><?php echo _('PLZ'); ?></label>
				<input id="city" name="city" type="text" value="<?php echo $city; ?>">
				<label for="city"><?php echo _('Ort'); ?></label>
				<select id="countryCode" name="countryCode">
					<option value=""><?php echo _('bitte auswählen'); ?></option>
				<?php 
					foreach($countries as $countryCode => $countryName) {
						showOption($countryCode, $countryName, $countryCode == $country);
					} 
				?>
				</select>
				<label for="countryCode"><?php echo _('Land'); ?></label>
				<select id="denomination" name="denomination">
					<option value=""><?php echo _('bitte auswählen'); ?></option>
					<?php 
					foreach($denominations as $value => $denominationName) {
						showOption($value, $denominationName, $value == $denomination);
					} 
					?>
				</select>
				<label for="denomination"><?php echo _('Konfession'); ?></label>
				<select id="type" name="type">
					<option value=""><?php echo _('bitte auswählen'); ?></option>
				<?php 
					foreach($types as $value => $typeName) {
						showOption($value, $typeName, $value == $type);
					}
				?>
				</select>
				<label for="type"><?php echo _('Gemeindetyp'); ?></label>
				<select id="hasWebsiteType" name="hasWebsiteType">
					<option value=""><?php echo _('bitte auswählen'); ?></option>
				<?php
					foreach($websites as $websiteId => $websiteName) {
						showOption($websiteId, $websiteName, $websiteId == $hasWebsiteType);
					} 
				?>
				</select>
				<label for="hasWebsiteType"><?php echo _('nur Gemeinden mit'); ?></label>
			</fieldset>
			<fieldset class="small-fieldset">
				<?php if (!$compare) {?>
				<legend><?php echo _('Anzeige'); ?></legend>
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
				<button type="submit"><?php echo _('Filtern'); ?></button>
			</fieldset>
		</form>
		
		<table id="churchTable" class="tablesorter">
			<thead>
				<tr>
					<th><?php echo _('Name'); ?></th>
					<th><?php echo _('PLZ'); ?></th>
					<th><?php echo _('Ort'); ?></th>
					<th><?php echo _('Land'); ?></th>
					<th><?php echo _('Konfession'); ?></th>
					<th><?php echo _('Gemeindetyp'); ?></th>					
				<?php 
					foreach ($showWebsites as $websiteValue => $websiteName) {
				?>
					<th class="<?php echo $websiteValue; ?>"><?php echo $websiteName; ?></th>
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
					<td><?php echo $denominations[$row['denomination']]; ?></td>
					<td><?php echo $types[$row['type']]; ?></td>
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
					<td class="number"><a href="<?php echo $url; ?>"><?php echo getNumberFormat($followers); ?></a></td>
				<?php 		
							} else { 
				?>
					<td><?php if (!$compare) {?><a href="<?php echo $url; ?>"><?php echo $websiteName; ?></a><?php } ?></td>
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
				echo _('Leider keine Gemeinde gefunden!');
			} else {
				echo sprintf( ngettext("Eine Gemeinde gefunden!", "%d Gemeinden gefunden!", $counter), $counter);
			} ?></output></p>
	</main>
	<script src="/js/jquery.min.js"></script>
	<script src="/js/jquery.tablesorter.js"></script>
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
		<?php if ($current_language == 'de_DE') { ?>
		usNumberFormat: false,
		<?php } else { ?>
		usNumberFormat: true,
		<?php } ?>
	});</script>	
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>