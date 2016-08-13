<?php 
	include_once 'includes/functions.php';

	$queryTotal = 'SELECT count(*) AS count FROM churches';
	$statementTotal = $connection->query($queryTotal);
	$totalCount = $statementTotal->fetch(PDO::FETCH_ASSOC);
	
	$queryByDenomination = 'SELECT count(*) AS count, denomination FROM churches GROUP BY denomination';
	$statementByDenomination = $connection->query($queryByDenomination);
	$totalByDenomination = $statementByDenomination->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByType = 'SELECT count(*) AS count, type FROM churches GROUP BY type';
	$statementByType= $connection->query($queryByType);
	$totalByType = $statementByType->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByCountry = 'SELECT count(*) AS count, country as countryCode FROM churches GROUP BY country ORDER BY count DESC';
	$statementByCountry = $connection->query($queryByCountry);
	$totalByCountry = $statementByCountry->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByWebsite = 'SELECT count(*) AS count, type FROM websites GROUP BY type ORDER BY count DESC';
	$statementByWebsite = $connection->query($queryByWebsite);
	$totalByWebsite = $statementByWebsite->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Statistik'); ?>: <?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<link rel="stylesheet" href="./css/style.css">
	<script type="text/javascript" src="./js/jquery.min.js"></script>
	<script type="text/javascript">
	$(function() {
		$('#denominations').highcharts({
			chart: {
				type: 'pie'
			},
			title: {
				text: '<?php echo _('Einträge nach Konfessionen'); ?>'
			},
			subtitle: {
				text: 'kirchen-im-web.de'
			},
			legend: {
				enabled: true,
			},
			series: [ {
				name: '<?php echo _('Anzahl der Einträge'); ?>',
				data: [<?php foreach ($totalByDenomination as $row) { 
					echo "{name: '" . $row['denomination'] . "', y:"; echo $row['count'] . '},'; 
				} ?> ]
			} ],
			colors: [
				'#FF8000', /* orange: Old Catholic */ 
				'#81A04D', /* green: Anglican */
				'#4E2775', /* purple: Protestant */
				'#2f7ed8', /* bright purple: free churches */
				'#E5CA4D', /* yellow: Catholic */
				'#FF4500'  /* red: oecumenic */
			],
			credits: {
				href: '',
				text: ''	
			}
		});
	});
	</script>
	<script>
	$(function() {
		$('#countries').highcharts({
			chart: {
				type: 'pie'
			},
			title: {
				text: '<?php echo _('Einträge nach Ländern'); ?>'
			},
			subtitle: {
				text: 'kirchen-im-web.de'
			},
			series: [ {
				name: '<?php echo _('Anzahl der Einträge'); ?>',
				data: [<?php foreach ($totalByCountry as $row) { 
							echo "{name: '" . $countries[$row['countryCode']] . "', y:"; echo $row['count'] . '},'; 
						} ?> ]
			} ],
			credits: {
				href: '',
				text: ''	
			}
		});
	});
	</script>
	<script>
	$(function() {
		$('#types').highcharts({
			chart: {
				type: 'pie'
			},
			title: {
				text: '<?php echo _('Einträge nach Gemeindetypen'); ?>'
			},
			subtitle: {
				text: 'kirchen-im-web.de'
			},
			series: [ {
				name: '<?php echo _('Anzahl der Einträge'); ?>',
				data: [<?php foreach ($totalByType as $row) { 
							echo "{name: '" . $row['type'] . "', y:"; echo $row['count'] . '},'; 
						} ?> ]
			} ],
			credits: {
				href: '',
				text: ''	
			}
		});
	});
	</script>	
	<script type="text/javascript">
	$(function() {
		$('#websites').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '<?php echo _('Gemeinden mit Webauftritt/Social-Media-Profilen'); ?>'
			},
			subtitle: {
				text: '<?php echo _('Gemeinden mit mehreren Auftritten mehrfach erfasst'); ?>'
			},
			xAxis: {
				categories: [ 
					<?php foreach ($totalByWebsite as $row) { 
						echo "'" . $websites[$row['type']] . "',";
					} ?> ],
				labels: {
					rotation: -45,
				}
			},
			yAxis: {
				title: {
					text: '<?php echo _('Anzahl der Einträge'); ?>'
				}
			},
			legend: {
				enabled: false,
			},
			plotOptions: {
	            line: {
	                dataLabels: {
	                    enabled: true
	                },
	                enableMouseTracking: false
	            }
	        },
			series: [ {
				name: '<?php echo _('Anzahl der Einträge'); ?>',
				data: [<?php foreach ($totalByWebsite as $row) { 
							echo $row['count'] . ','; 
						} ?> ]
			} ],
			credits: {
				href: '',
				text: ''	
			}
		});
	});
	</script>	
</head>
<body id="statistics">
	<?php include_once 'includes/header.php'; ?>
	<main>
		<h1><?php echo _('Statistik'); ?></h1>
		<p>Auf kirchen-im-web.de sind aktuell <?php echo $totalCount['count']; ?> Gemeinden erfasst.</p>
		
		<nav>
			<h2><?php echo _('Inhalt'); ?></h2>
			<ul>
				<li><a href="#statistics-denominations"><?php echo _('Einträge nach Konfessionen'); ?></a></li>
				<li><a href="#statistics-countries"><?php echo _('Einträge nach Ländern'); ?></a>
				<li><a href="#statistics-types"><?php echo _('Einträge nach Gemeindetypen'); ?></a></li>
				<li><a href="#statistics-networks"><?php echo _('Einträge nach Webauftritten/Social-Media-Auftritten'); ?></a></li>
			</ul>
		</nav>
		
		<script src="./js/highcharts.js"></script>
		<script src="./js/exporting.js"></script>
		
		<article id="statistics-denominations">
			<h2><?php echo _('Einträge nach Konfessionen'); ?></h2>
			<div class="stats">	
				<ul>
					<?php foreach ($totalByDenomination as $row) { ?>
					<li><a href="table.php?denomination=<?php echo $row['denomination']; ?>"><?php echo $row['denomination']; ?></a>: <?php echo $row['count']; ?></li>
					<?php } ?>
				</ul>
			</div>
			<div id="denominations" class="chart"></div>
		</article>
		<article id="statistics-countries">
			<h2><?php echo _('Einträge nach Ländern'); ?></h2>
			<div class="stats">
				<ul>
					<?php foreach ($totalByCountry as $row) { ?>
					<li><a href="table.php?countryCode=<?php echo $row['countryCode']; ?>"><?php echo $countries[$row['countryCode']]; ?></a>: <?php echo $row['count']; ?></li>
					<?php } ?>
				</ul>
			</div>
			<div id="countries" class="chart"></div>
		</article>
		<article id="statistics-types">
			<h2><?php echo _('Einträge nach Gemeindetypen'); ?></h2>
			<div class="stats">
				<ul>
					<?php foreach ($totalByType as $row) { ?>
					<li><a href="table.php?type=<?php echo $row['type']; ?>"><?php echo $row['type']; ?></a>: <?php echo $row['count']; ?></li>
					<?php } ?>
				</ul>
			</div>
			<div id="types" class="chart"></div>
		</article>
		<article id="statistics-networks">
			<h2><?php echo _('Einträge nach Webauftritten/Social-Media-Auftritten'); ?></h2>
			<div class="stats">	
				<p><?php echo _('In dieser Statistik sind Gemeinden mit mehreren Webauftritten/Social-Media-Auftritten natürlich mehrfach erfasst.'); ?></p>
				<ul>
					<?php foreach ($totalByWebsite as $row) { ?>
					<li><a href="table.php?hasWebsiteType=<?php echo $row['type']; ?>&<?php echo $row['type']; ?>=show"><?php echo $websites[$row['type']]; ?></a>: <?php echo $row['count']; ?></li>
					<?php } ?>
				</ul>
			</div>
			<div id="websites" class="chart"></div>
		</article>
	</main>	
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>