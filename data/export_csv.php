<?php
	require_once '../includes/config.php';

	$time_start = microtime(true);
	
	$date = date('Y-m-d');
	$filename = 'data.csv';
	$file = fopen($filename, 'w');
	
	// The headline.
	$headline = 'lat; lon; Name; Straße; PLZ; Ort; Land; Konfession; Typ';
	foreach ($websites as $websiteName) {
		$headline .= '; ' . $websiteName;
	}
	fwrite($file, $headline . "\n");
	
	// Query
	$query = 'SELECT id, lat, lon, name, street, postalCode, city, country, denomination, churches.type'; 
	foreach ($websites as $websiteId => $websiteName) {
		$query .= ', ' .$websiteId . '.url AS ' . $websiteId;	
	}
	$query .= ' FROM churches ';
	
	// .... get the URLs to show.
	foreach ($websites as $websiteId => $websiteName) {
		$query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.cid = churches.id 
				AND ' . $websiteId . '.type = "' . $websiteId . '" ';
	}
	
	$query .= 'ORDER BY id';
				
	$statement = $connection->query($query);
					
	while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$data = $row['lat'] . ';'
				. $row['lon'] . ';'
				. $row['name'] . ';'
				. $row['street'] . ';'		
				. $row['postalCode'] . ';' 
				. $row['city'] . ';'
				. $row['country'] . ';' 
				. $row['denomination'] . ';'
				. $row['type'] . ';'; 
		foreach ($websites as $websiteId => $websiteName) { 
			$data .= $row[$websiteId] . ';';
		}
		fwrite($file, $data . "\n");
	}
	
	fclose($file);

	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start);
	echo '<p>CSV Export completed in ' . $execution_time . ' seconds.</p>';
?>
