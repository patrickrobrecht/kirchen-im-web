<?php
	require_once '../includes/config.php';
	
	$time_start = microtime(true);
	
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
				
	$statement = $connection->query($query);
	
	$json = fopen('data.json', 'w');
	fwrite($json, stripslashes(json_encode($statement->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE)));
	fclose($json);

	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start);
	echo '<p>JSON Export completed in ' . $execution_time . ' seconds.</p>';
?>
