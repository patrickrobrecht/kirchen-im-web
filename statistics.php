<?php 
	include_once 'includes/functions.php';
	global $countries, $denominations, $types, $websites;

	$queryTotal = 'SELECT count(*) AS count FROM churches';
	$statementTotal = $connection->query($queryTotal);
	$totalCount = $statementTotal->fetch(PDO::FETCH_ASSOC);
	
	$queryByDenomination = 'SELECT count(*) AS count, denomination FROM churches GROUP BY denomination';
	$statementByDenomination = $connection->query($queryByDenomination);
	$totalByDenomination = $statementByDenomination->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByType = 'SELECT count(*) AS count, type FROM churches GROUP BY type ORDER BY count DESC';
	$statementByType= $connection->query($queryByType);
	$totalByType = $statementByType->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByCountry = 'SELECT count(*) AS count, country as countryCode FROM churches GROUP BY country ORDER BY count DESC';
	$statementByCountry = $connection->query($queryByCountry);
	$totalByCountry = $statementByCountry->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByWebsite = 'SELECT count(*) AS count, type FROM websites GROUP BY type ORDER BY count DESC';
	$statementByWebsite = $connection->query($queryByWebsite);
	$totalByWebsite = $statementByWebsite->fetchAll(PDO::FETCH_ASSOC);
	
	$queryByWebsiteHTTPS = 'SELECT w1.type, IFNULL(count, 0) as count, IFNULL(countHTTPS, 0) as countHTTPS  
		FROM (
    		SELECT count(*) AS count, type FROM websites
			WHERE type IN ("web", "rss", "blog") GROUP BY type
		) as w1
		LEFT JOIN (
    		SELECT count(*) AS countHTTPS, type FROM websites
			WHERE url LIKE "https://%" AND type IN ("web", "rss", "blog") GROUP BY type
		) as w2 
		ON w1.type = w2.type
		ORDER BY count DESC';
	$statementByWebsiteHTTPS = $connection->query($queryByWebsiteHTTPS);
	$totalByWebsiteHTTPS = $statementByWebsiteHTTPS->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Statistik'); ?>: <?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>">
	<link rel="stylesheet" href="/public/css/style.css">
	<script type="text/javascript" src="/public/node_modules/jquery/dist/jquery.min.js"></script>
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
				enabled: true
			},
			series: [ {
				name: '<?php echo _('Anzahl der Einträge'); ?>',
				data: [<?php foreach ($totalByDenomination as $row) { 
					echo "{name: '" . $denominations[$row['denomination']] . "', y:"; echo $row['count'] . '},'; 
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
							echo "{name: '" . $types[$row['type']] . "', y:"; echo $row['count'] . '},'; 
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
				type: 'bar'
			},
			title: {
				text: '<?php echo _('Gemeinden mit Webauftritt/Social-Media-Profilen'); ?>'
			},
			subtitle: {
				text: 'kirchen-im-web.de'
			},
			xAxis: {
				categories: [ 
					<?php foreach ($totalByWebsite as $row) { 
						echo "'" . $websites[$row['type']] . "',";
					} ?> ],
				labels: {
					rotation: -45
				}
			},
			yAxis: {
				title: {
					text: '<?php echo _('Anzahl der Einträge'); ?>'
				}
			},
			legend: {
				enabled: false
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
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
	<script type="text/javascript">
	$(function() {
		$('#https').highcharts({
			chart: {
				type: 'bar'
			},
			title: {
				text: '<?php echo _('Verschlüssung der Webauftritte'); ?>'
			},
			subtitle: {
				text: 'kirchen-im-web.de'
			},
			xAxis: {
				categories: [ 
					<?php foreach ($totalByWebsiteHTTPS as $row) { 
						echo "'" . $websites[$row['type']] . "',";
					} ?> ],
				labels: {
					rotation: -45
				}
			},
			yAxis: {
				title: {
					text: '<?php echo _('Anzahl der Einträge'); ?>'
				}
			},
			legend: {
				enabled: true
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			series: [ {
				name: 'HTTP',
				data: [<?php foreach ($totalByWebsiteHTTPS as $row) { 
							echo ($row['count'] - $row['countHTTPS']) . ','; 
						} ?>]
			}, { 
				name: 'HTTPS',
				data: [<?php foreach ($totalByWebsiteHTTPS as $row) { 
							echo $row['countHTTPS'] . ','; 
						} ?>]
			}],
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
		<p><?php echo sprintf( _('Auf kirchen-im-web.de sind aktuell %s Gemeinden erfasst.'), $totalCount['count'] ); ?></p>
		
		<nav>
			<h2><?php echo _('Inhalt'); ?></h2>
			<ul>
				<li><a href="#statistics-denominations"><?php echo _('Einträge nach Konfessionen'); ?></a></li>
				<li><a href="#statistics-countries"><?php echo _('Einträge nach Ländern'); ?></a>
				<li><a href="#statistics-types"><?php echo _('Einträge nach Gemeindetypen'); ?></a></li>
				<li><a href="#statistics-networks"><?php echo _('Einträge nach Webauftritten/Social-Media-Auftritten'); ?></a></li>
				<li><a href="#statistics-https"><?php echo _('Verschlüssung der Webauftritte'); ?></a></li>
			</ul>
		</nav>
		
		<script src="/public/js/highcharts.js"></script>
		<script src="/public/js/exporting.js"></script>
		
		<article id="statistics-denominations">
			<h2><?php echo _('Einträge nach Konfessionen'); ?></h2>
			<div id="denominations" class="chart"></div>
			<div class="stats">	
				<table>
					<thead>
						<tr>
							<th><?php echo _('Konfession'); ?></th>
							<th colspan="2"><?php echo _('Anzahl der Einträge'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($totalByDenomination as $row) { ?>
						<tr>
							<td><a class="<?php echo $row['denomination']; ?>" href="table.php?denomination=<?php echo $row['denomination']; ?>"><?php echo $denominations[$row['denomination']]; ?></a></td>
							<td class="number"><?php echo $row['count']; ?></td>
							<td class="number">(<?php echo getNumberFormat($row['count']/$totalCount['count']*100, 1); ?> %)</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</article>
		<article id="statistics-countries">
			<h2><?php echo _('Einträge nach Ländern'); ?></h2>
			<div id="countries" class="chart"></div>
			<div class="stats">
				<table>
					<thead>
						<tr>
							<th><?php echo _('Land'); ?></th>
							<th colspan="2"><?php echo _('Anzahl der Einträge'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($totalByCountry as $row) { ?>
						<tr>
							<td><a class="<?php echo strtolower($row['countryCode']); ?>" href="table.php?countryCode=<?php echo $row['countryCode']; ?>"><?php echo $countries[$row['countryCode']]; ?></a></td>
							<td class="number"><?php echo $row['count']; ?></td>
							<td class="number">(<?php echo getNumberFormat($row['count']/$totalCount['count']*100, 1); ?> %)</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</article>
		<article id="statistics-types">
			<h2><?php echo _('Einträge nach Gemeindetypen'); ?></h2>
			<div id="types" class="chart"></div>
			<div class="stats">
				<table>
					<thead>
						<tr>
							<th><?php echo _('Gemeindetyp'); ?></th>
							<th colspan="2"><?php echo _('Anzahl der Einträge'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($totalByType as $row) { ?>
						<tr>
							<td><a href="table.php?type=<?php echo $row['type']; ?>"><?php echo $types[$row['type']]; ?></a></td>
							<td class="number"><?php echo $row['count']; ?></td>
							<td class="number">(<?php echo getNumberFormat($row['count']/$totalCount['count']*100, 1); ?> %)</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</article>
		<article id="statistics-networks">
			<h2><?php echo _('Einträge nach Webauftritten/Social-Media-Auftritten'); ?></h2>
			<div id="websites" class="chart"></div>
			<div class="stats">
				<table>
					<thead>
						<tr>
							<th><?php echo _('Webauftritt/Netzwerk'); ?></th>
							<th colspan="2"><?php echo _('Anzahl der Einträge'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($totalByWebsite as $row) { ?>
						<tr>
							<td><a class="<?php echo $row['type']; ?>" href="table.php?hasWebsiteType=<?php echo $row['type']; ?>&<?php echo $row['type']; ?>=show"><?php echo $websites[$row['type']]; ?></a></td>
							<td class="number"><?php echo $row['count']; ?></td>
							<td class="number">(<?php echo getNumberFormat($row['count']/$totalCount['count']*100, 1); ?> %)</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</article>
		<article id="statistics-https">
			<h2><?php echo _('Verschlüssung der Webauftritte'); ?></h2>
			<div id="https" class="chart"></div>
			<div class="stats">
				<table>
					<thead>
						<tr>
							<td></td>
							<th><?php echo _('alle'); ?></th>
							<th colspan="2">HTTPS</th>
							<th colspan="2">HTTP</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($totalByWebsiteHTTPS as $row) { ?>
						<tr>
							<td><a class="<?php echo $row['type']; ?>" href="table.php?hasWebsiteType=<?php echo $row['type']; ?>&<?php echo $row['type']; ?>=show"><?php echo $websites[$row['type']]; ?></a></td>
							<td class="number"><?php echo $row['count']; ?></td>
							<td class="number"><?php echo $row['countHTTPS']; ?></td>
							<td class="number">(<?php echo getNumberFormat( $row['countHTTPS']/$row['count']* 100, 1); ?> %)</td>
							<td class="number"><?php echo ($row['count'] - $row['countHTTPS']); ?></td>
							<td class="number">(<?php echo getNumberFormat( ($row['count'] - $row['countHTTPS'])/$row['count']*100, 1); ?> %)</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</article>
	</main>
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>