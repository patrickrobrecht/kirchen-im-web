<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1>Consistency checks</h1>
		<ul>
			<li><a href="#space">Space Characters</a></li>
			<li><a href="#geo">Missing geopositions</a></li>
			<li><a href="#social">Check for missing follower data for the social network comparison</a></li>
			</li>
		</ul>
<?php
	$time_start = microtime(true);

	$check = isset( $_GET['check'] ) ? $_GET['check'] : '';
	
	$min = isset( $_GET['min'] ) ? intval($_GET['min']) : 0; 
	$max = isset( $_GET['max'] ) ? intval($_GET['max']) : 10000;
?>

	<h1 id="space">Check for spaces at the start and end of name, street, city</h1>
	<?php		
		$statement = $connection->prepare('SELECT id, name, street, city from churches WHERE id >= :min AND id <= :max');
		$statement->bindParam(':min', $min);
		$statement->bindParam(':max', $max);
		$statement->execute();
		
		echo '<p>Too much space in name: ';
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			if ($row['name'] != trim($row['name'])) {
				echo $row['id'] . ', ';
			}
		}
		echo '</p>';
		
		echo '<p>Too much space in street: ';
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			if ($row['street'] != trim($row['street'])) {
				echo $row['id'] . ', ';
			}
		}
		echo '</p>';
		
		$statement->execute();
		echo '<p>Too much space in city: ';
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			if ($row['city'] != trim($row['city'])) {
				echo $row['id'] . ', ';
			}
		}
		echo '</p>';
	?>
	
	<h1 id="geo">Check for missing geolocation data</h1>
	<?php 	
		$statement = $connection->prepare('SELECT * FROM `churches` WHERE lat is null or lon is null AND id >= :min AND id <= :max');
		$statement->bindParam(':min', $min);
		$statement->bindParam(':max', $max);
		$statement->execute();
		
		echo '<p>lat or lon missing: ';
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			echo $row['id'] . ', ';
		}
		echo '</p>';
	?>

	<h1 id="social">Check for missing follower data for the social network comparison</h1>
	<?php 
		$networksToCompareAsStrings = array();
		foreach($networksToCompare as $type => $typeName) {
			array_push($networksToCompareAsStrings, "'" . $type . "'");
		}
		$networksToCompareList = implode(', ', $networksToCompareAsStrings);
		
		$statement = $connection->prepare('SELECT cid, url from websites 
			WHERE type IN (' . $networksToCompareList . ') 
				AND (followers IS NULL OR followers = 0) ORDER BY type, cid');
		$statement->execute();
		
		echo '<ol>';
		
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			echo '<li><a href="details.php?id=' . $row['cid'] . '">'. $row['cid'] . '</a> ' .
				'<a href="' . $row['url'] . '">' . $row['url'] . '</a></li>';
		}
		
		echo '</ol>';
		
		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start);
		echo '<p>Check completed in ' . number_format($execution_time, 3) . ' seconds.</p>';
	?>
	</main>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>