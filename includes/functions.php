<?php
	require_once 'includes/config.php';
	
function echo_language() {
	global $current_language;
	echo $current_language;
}
	
/**
 * Tests whether the given variable is null or the empty string.
 * 
 * @param unknown $str
 * @return boolean
 */
function isNullOrEmptyString($str){
	return (!isset($str) || trim($str) === '');
}

/**
 * Tests whether the given variable is a valid name.
 * 
 * @param unknown $name
 * @return boolean
 */
function isName($name) {
	return !isNullOrEmptyString($name);
}

/**
 * Tests whether the given variable is a valid street.
 * 
 * @param unknown $street
 * @return boolean
 */
function isStreet($street) {
	return !isNullOrEmptyString($street);
}

/**
 * Tests whether the given variable is a valid postal code for the given country.
 * 
 * @param unknown $postalCode
 * @param unknown $countryCode
 * @return number
 */
function isPostalCode($postalCode, $countryCode) {
	switch ($countryCode) {
		case 'DE':
			return preg_match('/[0-9]{5}/', $postalCode);
			break;
		case 'AT':
		case 'CH':
		case 'LI':
		case 'LU':
			return preg_match('/[0-9]{4}/', $postalCode);
			break;
	}
}

/**
 * Tests whether the given variable is a valid city name.
 * 
 * @param unknown $city
 * @return boolean
 */
function isCity($city) {
	return !isNullOrEmptyString($city);
}

/**
 * Tests whether the given variable is a valid country code.
 * 
 * @param unknown $countryCode
 * @return boolean
 */
function isCountryCode($countryCode) {
	global $countries;
	return !isNullOrEmptyString($countryCode) && array_key_exists($countryCode, $countries);
}

/**
 * Tests whether the given variable is a valid denomination.
 * 
 * @param unknown $denomination
 * @return boolean
 */
function isDenomination($denomination) {
	global $denominations;
	return !isNullOrEmptyString($denomination) && in_array($denomination, $denominations);
}

/**
 * Tests whether the given variable is a valid type.
 * 
 * @param unknown $type
 * @return boolean
 */
function isType($type) {
	global $types;
	return !isNullOrEmptyString($type) && in_array($type, $types);
}

/**
 * Tests whether the given variable is a valid parent id.
 * 
 * @param unknown $parentId
 * @return boolean
 */
function isParentId($parentId) {
	return true;
}

/**
 * Tests whether the given variable is a valid URL.
 * 
 * @param unknown $url
 * @return boolean
 */
function isURL($url) {
	return ($url && is_string($url) && $url != ''
			&& preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url));
}

/**
 * Tests whether the given variable is a valid URL with begins with the given string.
 * 
 * @param unknown $url
 * @param string $startsWith
 * @return boolean
 */
function isValidURL($url, $startsWith = '') {
	return isURL($url) && startsWith($url, $startsWith);
}

/**
 * Tests whether the given haystack starts with needle.
 * 
 * @param unknown $haystack
 * @param unknown $needle
 * @return boolean
 */
function startsWith($haystack, $needle) {
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

/**
 * Adds a church with the given data to the database.
 * 
 * @param unknown $data
 * @param unknown $urls
 */
function addChurchToDatabase($data, $urls) {
	// Add church to the database.
	global $connection;
	$statement = $connection->prepare('
			INSERT INTO churches (name, street, postalCode, city, country, lat, lon, denomination, type, hasChildren, timestamp)
			VALUES (:name, :street, :postalCode, :city, :countryCode, :lat, :lon, :denomination, :type, :hasChildren, NOW())
	');
	
	$geolocation = getGeolocation($data['street'], $data['city'], $data['countryCode']);
	
	$statement->bindParam(':name', $data['name']);
	$statement->bindParam(':street', $data['street']);
	$statement->bindParam(':postalCode', $data['postalCode'], PDO::PARAM_INT);
	$statement->bindParam(':city', $data['city']);
	$statement->bindParam(':countryCode', $data['countryCode']);
	$statement->bindParam(':lat', $geolocation['lat']);
	$statement->bindParam(':lon', $geolocation['lon']);
	$statement->bindParam(':denomination', $data['denomination']);
	$statement->bindParam(':type', $data['type']);
	$statement->bindParam(':hasChildren', $data['hasChildren'], PDO::PARAM_INT);
	$statement->execute();
	
	$churchId = $connection->lastInsertId();
	
	// Set parent id.
	if ($data['parentId'] != 0) {
		$statement = $connection->prepare('
				UPDATE churches SET parentId = :parentId
				WHERE id = :id
		');
		$statement->bindParam(':parentId', $data['parentId'], PDO::PARAM_INT);
		$statement->bindParam(':id', $churchId, PDO::PARAM_INT);
		$statement->execute();
	}
	
	// Add the URLs to the database.
	$statement = $connection->prepare(
			'INSERT INTO websites (cid, type, url) 
			VALUES (:cid, :type, :url)'
	);
	foreach($urls as $type => $url) {
		$statement->bindParam(':cid', $churchId);
		$statement->bindParam(':type', $type);
		$statement->bindParam(':url', $url);
		$statement->execute();	
	}
	
	return $churchId;
}

function showOption($value, $text, $selected = false) {
	echo '<option value="' . $value . '"';
	if ($selected) {
		echo ' selected="selected"';
	}
	echo '>' . $text . '</option>';
}

function getGeolocation($street, $city, $countryCode) {
	$address = str_replace(" ", "+", $street . $city);
	global $countries;
	$region = $countries[$countryCode];
	
	$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
	$json = json_decode($json);
	
	$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
	$lon = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
	
	return array(
		'lat' => $lat,
		'lon' => $lon
	);
}

function echoLinkOrText($text, $url, $active) {
	echo '<li>';
	if ($active) {
		echo '<strong>' . $text . '</strong>';
	} else {
		echo '<a href="' . $url . '">' . $text . '</a>';
	}
	echo '</li>';
}

function echoLanguageLink($linkTitle, $slug, $self) {
	echo '<li>';
	echo '<a class="lang_' . $slug . '" href="/' . $slug . '/' . $self . '">' . $linkTitle . '</a>';
	echo '</li>';
}

function getLinkToDetailsPage($id, $text) {
	if (is_null($id)) {
		return '–';
	} else {
		return '<a href="details.php?id=' . $id . '">' . $text . '</a>';	
	}	
}

function GermanNumberFormat($number, $decimals = 0) {
	return number_format($number, $decimals, ',', '.');
}

function postalCodeString($postalCodeNumber, $countryCode) {
	if ($countryCode == 'DE') {
		return str_pad($postalCodeNumber, 5 ,'0', STR_PAD_LEFT);
	} else {
		return $postalCodeNumber;
	}
}

function geoPositionString($lat, $lon) {
	$geo = '';
	$geo .= $lat . '° ' . (($lat > 0) ? '<abbr title="nördliche Breite">N</abbr>' : '<abbr title="südliche Breite">S</<abbr>');
	$geo .= ', ';
	$geo .= $lon . '° ' . (($lon > 0) ? '<abbr title="östliche Länge">E</abbr>' : '<abbr title="westliche Länge">W</abbr>');
	return $geo; 
}

function getChurchesChildren($parent) {
	global $connection;
	$queryChildren = 'SELECT id, name FROM churches';
	$queryChildren .= ' WHERE parentId = ' . $parent;
	$queryChildren .= ' ORDER BY name';
	$statementChildren = $connection->query($queryChildren);
	return $statementChildren->fetchAll(PDO::FETCH_ASSOC);
}

function echoChurchesHierarchy($children) {
	if (count($children) > 0) {?>
	<ol>
		<?php foreach ($children as $child) {
			echo '<li>' . getLinkToDetailsPage($child['id'], $child['name']) . '</li>';
			$grandchildren = getChurchesChildren($child['id']);
			echoChurchesHierarchy($grandchildren);
		} ?>
	</ol>
	<?php }
}
