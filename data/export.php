<?php
	require_once dirname(__FILE__, 2) . '/includes/config.php';
	
	// Build Query
	$query = 'SELECT id, lat, lon, name, street, postalCode, city, country, denomination, churches.type';
	foreach ($websites as $websiteId => $websiteName) {
		$query .= ', ' .$websiteId . '.url AS ' . $websiteId;
	}
	$query .= ' FROM churches ';
	foreach ($websites as $websiteId => $websiteName) {
		$query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.cid = churches.id
					AND ' . $websiteId . '.type = "' . $websiteId . '" ';
	}
	
	$time_start = microtime(true);
	
	// Create files.
	create_json_file( $connection->query($query) );
	create_csv_file( $connection->query($query), $websites );
	
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start);
	echo '<p>Script completed in ' . $execution_time . ' seconds.</p>';

	
	function create_json_file( $statement ) {
		$filename = 'data-' . date('Y-m-d') . '.json';
		
		$json = fopen( $filename, 'w' );
		fwrite( $json, stripslashes( json_encode( $statement->fetchAll( PDO::FETCH_ASSOC ), JSON_UNESCAPED_UNICODE ) ) );
		fclose( $json );
		echo '<p>JSON Export completed</p>';
		
		copy( $filename, "data.json" );
		echo '<p>Copied ' . $filename . ' to data.json</p>';
	}
	
	function create_csv_file( $statement, $websites ) {
		$filename = 'data-' . date('Y-m-d') . '.csv';
		
		$file = fopen($filename, 'w');
		$headline = 'id;lat;lon;Name;Straße;PLZ;Ort;Land;Konfession;Typ';
		foreach ($websites as $websiteId => $websiteName) {
			$headline .= ';' . $websiteName;
		}
		fwrite($file, $headline . "\n");
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$data = implode(";", $row);
			fwrite($file, $data . "\n");
		}
		fclose($file);
		
		echo '<p>CSV Export completed</p>';
		
		copy( $filename, "data.csv" );
		echo '<p>Copied ' . $filename . ' to data.csv</p>';
	}
?>
