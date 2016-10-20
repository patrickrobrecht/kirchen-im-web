<?php 
	include_once '../includes/config.php';
	
	// Get the values for the filters.
	$id = isset( $_GET['id'] ) ? intval( trim( $_GET['id'] ) ) : ''; 
	$name = isset( $_GET['name'] ) ? trim( $_GET['name'] ) : '';
	$postalCode = isset( $_GET['postalCode'] ) ? intval( $_GET['postalCode'] ) : '';
	$city = isset( $_GET['city'] ) ? trim( $_GET['city'] ) : '';
	$country = isset( $_GET['countryCode'] ) ? trim( $_GET['countryCode'] ) : '';
	$denomination = isset( $_GET['denomination'] ) ? trim( $_GET['denomination'] ) : '';
	$type = isset( $_GET['type'] ) ? trim( $_GET['type'] ) : '';
	$hasWebsiteType = isset( $_GET['hasWebsiteType'] ) ? trim( $_GET['hasWebsiteType'] ) : '';

	// Query
	$query = 'SELECT id, name, postalCode, city, country, denomination, churches.type';
	foreach ( $websites as $websiteId => $websiteName ) {
		$query .= ', ' .$websiteId . '.url AS ' . $websiteId . ', ' . $websiteId . '.followers AS ' . $websiteId . '_followers';
	}
	$query .= ' FROM churches ';

	// .... get the URLs to show.
	foreach ( $websites as $websiteId => $websiteName ) {
		$query .= 'LEFT JOIN websites AS ' . $websiteId . ' ON ' . $websiteId . '.cid = churches.id
					AND ' . $websiteId . '.type = "' . $websiteId . '" ';
	}

	// ... and apply the filters
	$conditions = array();
	if ( $id != 0 ) {
		array_push( $conditions, 'id LIKE :id ' );
	}
	if ( $name != '' ) {
		array_push( $conditions, 'name LIKE :name ' );
	}
	if ( $postalCode != 0 ) {
		array_push( $conditions, 'postalCode = :postalCode ' );
	}
	if ( $city != '' ) {
		array_push( $conditions, 'city LIKE :city ' );
	}
	if ( $country != '' ) {
		array_push( $conditions, 'country = :country ' );
	}
	if ( $denomination != '' ) {
		array_push( $conditions, 'denomination = :denomination ' );
	}
	if ( $type != '' ) {
		array_push( $conditions, 'churches.type = :ctype ' );
	}
	if ( $hasWebsiteType != '' ) {
		array_push( $conditions, 'EXISTS (SELECT * FROM websites WHERE id = cid AND type = :wtype) ' );
	}
	
	if ( sizeof( $conditions ) > 0 ) {
		$whereclause = '';
		foreach ( $conditions as $condition ) {
			$whereclause .= ' AND ' . $condition;
		}
		$whereclause = preg_replace( '/ AND/', ' WHERE', $whereclause, 1 );
		$query .= $whereclause;
		$query .= 'ORDER BY country, postalCode';
	} else {
		$query .= 'ORDER BY churches.timestamp DESC, id DESC LIMIT 25';
	}
	
	$statement = $connection->prepare( $query );
	if ( $id != 0 ) {
		$statement->bindParam(':id', $id);
	}
	if ( $name != '' ) {
		$name = '%' . $name . '%';
		$statement->bindParam( ':name', $name );
	}
	if ( $postalCode != 0 ) {
		$statement->bindParam( ':postalCode', $postalCode );
	}
	if ( $city != '' ) {
		$city = '%' . $city . '%';
		$statement->bindParam( ':city', $city );
	}
	if ( $country != '' ) {
		$statement->bindParam( ':country', $country );
	}
	if ( $denomination != '' ) {
		$statement->bindParam( ':denomination', $denomination );
	}
	if ( $type != '' ) {
		$statement->bindParam( ':ctype', $type );
	}
	if ( $hasWebsiteType != '' ) {
		$statement->bindParam( ':wtype', $hasWebsiteType );
	}
	
	
	// Get the data
	$statement->execute();
	$data = $statement->fetchAll( PDO::FETCH_ASSOC );

	// Remove null values
	foreach ( $data as $row_id => $row ) {
		$data[ $row_id] = array_diff( $data[ $row_id ], array( null ) );
	}
	
	// Return the data in json format.
	header( 'Content-Type: application/json;charset=utf-8' );
	echo json_encode( $data, JSON_UNESCAPED_UNICODE );
?>
