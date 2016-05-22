<?php 
	include_once 'includes/functions.php';
	
	// Get the values for the filters.
	$id = isset($_GET['id']) ? intval($_GET['id']) : -1;
	
	$exists = false;
	if (is_int($id)) {
		// Query for the data
		$query = 'SELECT churches.id, churches.name, churches.street, churches.postalCode, churches.city, churches.country, 
				churches.lat, churches.lon, churches.denomination, churches.type, churches.hasChildren,
				parent.id AS parentId, parent.name AS parentName FROM churches';
		$query .= ' LEFT JOIN churches AS parent ON parent.id = churches.parentId';
		$query .= ' WHERE churches.id = ' . $id;
		$statement = $connection->query($query);
		$data = $statement->fetch(PDO::FETCH_ASSOC);
		
		if ($data) {
			$exists = true;

			// Query for the children
			$children = getChurchesChildren($id);
			
			// Query for the websites
			$queryWebsites = 'SELECT url, type, followers FROM websites';
			$queryWebsites .= ' WHERE cid = ' . $id;
			$queryWebsites .= ' ORDER BY type ASC';
			$statementWebsites = $connection->query($queryWebsites);
			$theWebsites = $statementWebsites->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	
	if (!$exists) {
		header("HTTP/1.0 404 Not Found");
		include("404.php");
		return;
	}
?>
<!DOCTYPE html>
<html lang="de-DE">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Kirchen im Web: <?php echo $data['name']; ?></title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.">
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php displayHeader('details.php'); ?>
	
	<main>
		<h1>Details: <?php echo $data['name']; ?></h1>

		<section>
			<h2>Webseiten</h2>
			<table class="websites">
				<thead>
					<tr>
						<th>Link</th>
						<th>Follower</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($theWebsites as $website) { ?>
					<tr>
						<td><a href="<?php echo $website['url']; ?>"><?php echo $websites[$website['type']]; ?></a></td>
						<td class="number"><?php if (!is_null($website['followers']))					
							echo GermanNumberFormat($website['followers']);
						}?></td>
					</tr>
				</tbody>
			</table>
		</section>		
		
		<section>
			<h2>Adresse, Konfession und Hierarchie</h2>
			<table class="details">
				<tbody>
					<tr>
						<th>Adresse</th>
						<td><?php echo $data['street']; ?>, 
							<a href="table.php?postalCode=<?php echo postalCodeString($data['postalCode'], $data['country']); ?>"><?php echo postalCodeString($data['postalCode'], $data['country']);?></a>
							<a href="table.php?city=<?php echo $data['city']; ?>"><?php echo $data['city']; ?></a>,
							<a href="table.php?countryCode=<?php echo $data['country']; ?>"><?php echo $countries[$data['country']]; ?></a></td>
					</tr>
					<tr>
						<th>Geopositon</th>
						<td><?php echo geoPositionString($data['lat'], $data['lon']); ?>
					<tr>					
						<th>Konfession</th>
						<td><a href="table.php?countryCode=<?php echo $data['denomination']; ?>"><?php echo $data['denomination']; ?></a></td>
					</tr>
					<tr>					
						<th>Typ</th>
						<td><a href="table.php?type=<?php echo $data['type']; ?>"><?php echo $data['type']; ?></a></td>
					</tr>
					<tr>
						<th>nächsthöhere Ebene</th>
						<td><?php echo getLinkToDetailsPage($data['parentId'], $data['parentName']) ?></td>
					</tr>
					<tr>
						<th>nächsttiefere Ebene</th>
						<td><?php if (count($children) > 0) {
								echoChurchesHierarchy($children);
							} else {	
								echo 'Bisher wurden keine untergeordneten Ebenen hinzugefügt!';
							}
							if ($data['hasChildren'] == '1') {
								echo '<p><a href="add.php?parentId=' . $data['id'] . '">Jetzt eine Gemeinde unterhalb von '
									. $data['name'] . ' hinzufügen!</a></p>';
							}
							?></td>
					</tr>
				</tbody>
			</table>
		</section>
	</main>

	<?php displayFooter('details.php') ?>
</body>
</html>
