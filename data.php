<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="de-DE">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title>Offene Daten: Kirchliche Web- und Social-Media-Auftritte</title>
	<meta name="description" content="Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte. Das Projekt kirchen-im-web.de macht diese sichtbar.">
	<link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<?php displayHeader('data.php'); ?>
	
	<main>
		<h1>Offene Daten</h1>
		
		<article id="download">
			<h2>Download</h2>	
	
			<p>Die Daten stehen in einem freien und offenen Format zur Verfügung, d. h. die Daten können auch für andere Projekte verwendet werden.</p>
			<ul>
				<li><a href="data/data.json">Download JSON</a></li>
				<li><a href="data/data.csv">Download csv</a> (Trennzeichen: Semikolon, da einige der Namen ein Komma enthalten)</li>
			</ul>
			
			<p>Hinweis: Die Daten werden jeden Tag über Nacht neu in die beiden Formate exportiert.</p>
		</article>
		
		<article id="last-added">
			<h2>Zuletzt hinzugefügt</h2>
			<?php $showWebsites = $preselected; ?>
			<table>
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
						$query .= ', ' .$websiteId . '.url AS ' . $websiteId;	
					}
					$query .= ' FROM churches ';
					
					// .... get the URLs to show.
					foreach ($showWebsites as $websiteId => $websiteName) {
						$query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.cid = churches.id 
								AND ' . $websiteId . '.type = "' . $websiteId . '" ';
					}
	
					$query .= ' ORDER BY id DESC LIMIT 25';
					
					$statement = $connection->prepare($query);
					$statement->execute();
					
					while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
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
	
	<?php displayFooter('data.php') ?>
</body>
</html>