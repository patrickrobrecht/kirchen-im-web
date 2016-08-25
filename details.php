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
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo $data['name']; ?> - kirchen-im-web.de</title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<link rel="stylesheet" href="/css/style.css">
	<link rel="stylesheet" href="/css/leaflet.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	
	<main>
		<h1><?php echo _('Details'); ?>: <?php echo $data['name']; ?></h1>
						
		<section>
			<h2><?php echo _('Webauftritte und soziale Netzwerke'); ?></h2>
			<table class="websites">
				<thead>
					<tr>
						<th><?php echo _('Link'); ?></th>
						<th><?php echo _('Follower'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($theWebsites as $website) { ?>
					<tr>
						<td><a class="<?php echo $website['type']; ?>" href="<?php echo $website['url']; ?>"><?php echo $websites[$website['type']]; ?></a></td>
						<td class="number"><?php if (!is_null($website['followers']))					
							echo getNumberFormat($website['followers']);
						}?></td>
					</tr>
				</tbody>
			</table>
		</section>
		
		<section>
			<h2><?php echo _('Adresse, Konfession und Hierarchie'); ?></h2>

		<div id="detailsmap"><?php echo _('Bitte warten. Die Karte wird geladen.'); ?></div>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/leaflet.js"></script>
		<script type="text/javascript">
			var map;
			var markerArray = [];
			
			loadMap();
			
			function loadMap() {
				'use strict'; // Strict mode makes the browse mourn, if bad practise is used
				// create a map in the "map" div, set the view to a given place and zoom
				map = L.map('detailsmap').setView([<?php echo $data['lat']; ?>, <?php echo $data['lon']; ?>], 13);
				// add an OpenStreetMap tile layer
				L.tileLayer('http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
							attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> (CC BY-SA)'
					}).addTo(map);

				// add the marker
				var icon = L.icon({iconUrl: '/images/markers/<?php echo $denominations_colors[$data['denomination']]; ?>.png'});
				var thisMarker = L.marker([<?php echo $data['lat']; ?>, <?php echo $data['lon']; ?>], {title: "<?php echo $data['name']; ?>", icon: icon});
				thisMarker.addTo(map).bindPopup('<strong><?php echo $data['name']; ?></strong>');

				// Push the marker to the Array which shall be displayed on the map
				markerArray.push(thisMarker);
				
				var group = L.featureGroup(markerArray).addTo(map);
				map.fitBounds(group.getBounds());
			};
		</script>			
			
			<table class="details">
				<tbody>
					<tr>
						<th><?php echo _('Adresse'); ?></th>
						<td><?php echo $data['street']; ?>, 
							<a href="table.php?postalCode=<?php echo postalCodeString($data['postalCode'], $data['country']); ?>"><?php echo postalCodeString($data['postalCode'], $data['country']);?></a>
							<a href="table.php?city=<?php echo $data['city']; ?>"><?php echo $data['city']; ?></a>,
							<a href="table.php?countryCode=<?php echo $data['country']; ?>"><?php echo $countries[$data['country']]; ?></a></td>
					</tr>
					<tr>
						<th><?php echo _('Geopositon'); ?></th>
						<td><?php echo geoPositionString($data['lat'], $data['lon']); ?>
					<tr>					
						<th><?php echo _('Konfession'); ?></th>
						<td><a href="table.php?denomination=<?php echo $data['denomination']; ?>"><?php echo $denominations[$data['denomination']]; ?></a></td>
					</tr>
					<tr>					
						<th><?php echo _('Gemeindetyp'); ?></th>
						<td><a href="table.php?type=<?php echo $data['type']; ?>"><?php echo $types[$data['type']]; ?></a></td>
					</tr>
					<tr>
						<th><?php echo _('nächsthöhere Ebene'); ?></th>
						<td><?php echo getLinkToDetailsPage($data['parentId'], $data['parentName']) ?></td>
					</tr>
					<tr>
						<th><?php echo _('nächsttiefere Ebene'); ?></th>
						<td><?php if (count($children) > 0) {
								echoChurchesHierarchy($children);
							} else {	
								echo _('Bisher wurden keine untergeordneten Ebenen hinzugefügt!');
							}
							if ($data['hasChildren'] == '1') {
								echo '<p><a href="add.php?parentId=' . $data['id'] . '&denomination=' . $data['denomination'] .'&countryCode=' . $data['country'] . '">' 
									. sprintf( _('Jetzt eine Gemeinde unterhalb von %s hinzufügen!'), $data['name'] ) . '</a></p>';
							}
							?></td>
					</tr>
				</tbody>
			</table>
		</section>
	</main>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>