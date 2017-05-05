<?php 
	include_once 'includes/functions.php';
    global $countries, $preselected;
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Offene Daten'); ?> - <?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Offene Daten'); ?></h1>
		
		<article id="download">
			<h2><?php echo _('Download');?></h2>	
			<p><?php echo _('Die Daten stehen in einem freien und offenen Format zur Verfügung, d. h. die Daten können auch für andere Projekte verwendet werden.'); ?></p>
			<ul>
				<li><a href="./data/data.json"><?php echo _('Download JSON'); ?></a></li>
				<li><a href="./data/data.csv"><?php echo _('Download csv'); ?></a> (<?php echo _('Trennzeichen: Semikolon, da einige der Namen ein Komma enthalten'); ?>)</li>
			</ul>
			<p><?php echo _('Hinweis: Die Daten werden täglich neu in die beiden Formate exportiert.'); ?></p>
		</article>
		
		<article id="recently-added">
			<h2><?php echo _('Zuletzt hinzugefügt'); ?></h2>
			<?php $showWebsites = $preselected; ?>
			<table>
				<thead>
					<tr>
						<th><?php echo _('Name'); ?></th>
						<th><?php echo _('PLZ'); ?></th>
						<th><?php echo _('Ort'); ?></th>
						<th><?php echo _('Land'); ?></th>
						<th><?php echo _('Konfession'); ?></th>
						<th><?php echo _('Gemeindetyp'); ?></th>
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
						$query .= ', ' .$websiteId . '.url AS ' . $websiteId;	
					}
					$query .= ' FROM churches ';
					
					// .... get the URLs to show.
					foreach ($showWebsites as $websiteId => $websiteName) {
						$query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.cid = churches.id 
								AND ' . $websiteId . '.type = "' . $websiteId . '" ';
					}
	
					$query .= ' ORDER BY churches.timestamp DESC, id DESC LIMIT 25';
					
					$statement = $connection->prepare($query);
					$statement->execute();

					while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
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
							if ($url == '') {
					?>
						<td></td>
					<?php 	
							} else { 
					?>
						<td><a href="<?php echo $url; ?>"><?php echo $websiteName; ?></a></td>
					<?php 
							}
						} 
					?>
					</tr>
					<?php
					}
				?>
				</tbody>
			</table>
		</article>	
	</main>
	
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>