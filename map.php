<?php 
	include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo_language(); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<title><?php echo _('Karte'); ?>: <?php echo _('Kirchliche Web- und Social-Media-Auftritte'); ?></title>
	<meta name="description" content="<?php echo _('Viele Kirchengemeinden nutzen mittlerweile Social-Media-Auftritte.'); ?>
		<?php echo _('kirchen-im-web.de macht diese auf einer Karte sichtbar.'); ?>">
	<link rel="stylesheet" href="./css/style.css">
	<link rel="stylesheet" href="./css/leaflet.css">
</head>
<body>
	<?php include_once 'includes/header.php'; ?>	
	<main>
		<h1><?php echo _('Karte kirchlicher Web- und Social-Media-Auftritte'); ?></h1>
		<p><?php echo sprintf( _('In der %s sind alt-katholische Kirchen orange, anglikanische grün, evangelische lila, freikirchliche blau, katholische gelb und andere/ökumenische rot dargestellt.'), _('Karte') ); ?></p>
		<p><?php echo sprintf( _('Wenn Ihre Gemeinde noch fehlt, können Sie diese über %s eintragen.'), '<a href="add.php">' . _('dieses Formular') . '</a>'); ?></p>
		<div id="map"><?php echo _('Bitte warten. Die Karte wird geladen.'); ?></div>
	</main>
	
	<script src="js/jquery.min.js"></script>
	<script src="js/leaflet.js"></script>
	<script type="text/javascript">
		var map;
		var markerArray = [];
		// the layers
		var allLayer = new L.LayerGroup;
		var oldCatholicLayer = new L.LayerGroup;
		var anglicanLayer = new L.LayerGroup;
		var protestantLayer = new L.LayerGroup;
		var freeChurchesLayer = new L.LayerGroup;
		var catholicLayer = new L.LayerGroup;
		var othersLayer = new L.LayerGroup;
		var webLayer = new L.LayerGroup;
		var blogLayer = new L.LayerGroup;
		var rssLayer = new L.LayerGroup;
		var facebookLayer = new L.LayerGroup;
		var flickrLayer = new L.LayerGroup;
		var googleLayer = new L.LayerGroup;
		var instagramLayer = new L.LayerGroup;
		var soundcloudLayer = new L.LayerGroup;
		var twitterLayer = new L.LayerGroup;
		var vimeoLayer = new L.LayerGroup;
		var youtubeLayer = new L.LayerGroup;
		// the icons
		var oldCatholicIcon = L.icon({iconUrl: './images/markers/orange.png'});
		var anglicanIcon = L.icon({iconUrl: './images/markers/green.png'});
		var protestantIcon = L.icon({iconUrl: './images/markers/purple.png'});
		var freeChurchesIcon = L.icon({iconUrl: './images/markers/blue.png'});
		var catholicIcon = L.icon({iconUrl: './images/markers/yellow.png'});
		var othersIcon = L.icon({iconUrl: './images/markers/red.png'});
		
		loadMap();
		
		function loadMap() {
			'use strict'; // Strict mode makes the browse mourn, if bad practise is used
			// create a map in the "map" div, set the view to a given place and zoom
			map = L.map('map', {layers: [allLayer]}).setView([25, 8], 11);
			// add an OpenStreetMap tile layer
			L.tileLayer('http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
						attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> (CC BY-SA)'
				}).addTo(map);
			getData();
		};

		function getData() {
			'use strict';
			var url = 'data/data.json';
			console.log(url);
			$.getJSON(url,
				function(data) {
					var lat, lng, title, denomination, icon, denominationLayer,
						web, blog, rss, facebook, flickr, googlePlus, instagram, soundcloud, twitter, vimeo, youtube, 
						content, thisMarker;
					$.each(data,
						function(i, val) {
							// read the JSON data
							lng = val.lon;
							lat = val.lat;
							title = val.name;
							denomination = val.denomination;
							if (denomination == 'alt-katholisch') {
								denominationLayer = oldCatholicLayer;
								icon = oldCatholicIcon;
							} else if (denomination == 'anglikanisch') {
								denominationLayer = anglicanLayer;
								icon = anglicanIcon;
							} else if (denomination == 'evangelisch') {
								denominationLayer = protestantLayer;
								icon = protestantIcon;
							} else if (denomination == 'freikirchlich') {
								denominationLayer = freeChurchesLayer;
								icon = freeChurchesIcon;
							} else if (denomination == 'katholisch') {
								denominationLayer = catholicLayer;
								icon = catholicIcon;
							} else {
								denominationLayer = othersLayer;
								icon = othersIcon;
							}
							web = val.web;
							blog = val.blog;
							rss = val.rss;
							facebook = val.facebook;
							flickr = val.flickr;
							googlePlus = val.googlePlus;
							instagram = val.instagram;
							soundcloud = val.soundcloud;
							twitter = val.twitter;
							vimeo = val.vimeo;
							youtube = val.youtube;
							content = '<strong><a href="details.php?id=' + val.id + '">' + title + '</a></strong><br>' + val.street + ', ' + val.postalCode + ' ' + val.city + '<br><ul>';
							if (web) {
								content = content + '<li><a href="' + web + '"><?php echo $websites['web']; ?></a></li>';
							}
							if (blog) {
								content = content + '<li><a href="' + blog + '"><?php echo $websites['blog']; ?></a></li>';
							}
							if (rss) {
								content = content + '<li><a href="' + rss + '">RSS</a></li>';
							}
							if (facebook) {
								content = content + '<li><a href="' + facebook + '">Facebook</a></li>';
							}
							if (flickr) {
								content = content + '<li><a href="' + flickr + '">Flickr</a></li>';
							}
							if (googlePlus) {
								content = content + '<li><a href="' + googlePlus + '">Google+</a></li>';
							}
							if (instagram) {
								content = content + '<li><a href="' + instagram + '">Instagram</a></li>';
							}
							if (soundcloud) {
								content = content + '<li><a href="' + soundcloud + '">Soundcloud</a></li>';
							}
							if (twitter) {
								content = content + '<li><a href="' + twitter + '">Twitter</a></li>';
							}
							if (vimeo) {
								content = content + '<li><a href="' + vimeo + '">Vimeo</a></li>';
							}
							if (youtube) {
								content = content + '<li><a href="' + youtube + '">YouTube</a></li>';
							}
							content = content + '</ul>';
							// Build the popup for this row (the small window which you get when clicking on the marker)
							if (lat && lng && title && content) {
								thisMarker = L.marker([lat, lng], {title: title, icon: icon});
								thisMarker.addTo(map).bindPopup(content);
								// Push the marker to the Array which shall be displayed on the map
								markerArray.push(thisMarker);
								// add to the layers for the denominations
								thisMarker.addTo(allLayer);
								thisMarker.addTo(denominationLayer);
								// add to the layers for the social networks
								if (web) {
									thisMarker.addTo(webLayer);
								}
								if (blog) {
									thisMarker.addTo(blogLayer);
								}
								if (rss) {
									thisMarker.addTo(rssLayer);
								}
								if (facebook) {
									thisMarker.addTo(facebookLayer);
								}
								if (flickr) {
									thisMarker.addTo(flickrLayer);
								}
								if (googlePlus) {
									thisMarker.addTo(googleLayer);
								}
								if (instagram) {
									thisMarker.addTo(instagramLayer);
								}
								if (soundcloud) {
									thisMarker.addTo(soundcloudLayer);
								}
								if (twitter) {
									thisMarker.addTo(twitterLayer);
								}
								if (vimeo) {
									thisMarker.addTo(vimeoLayer);
								}
								if (youtube) {
									thisMarker.addTo(youtubeLayer);
								}
							} else {
								console.log('Problem with lat/lng for data entry ' + title);
							}
						});
						// add control for the layers
						var layers = {
							"<?php echo _('alle'); ?>": allLayer,
							"<?php echo _('alt-katholisch'); ?>": oldCatholicLayer,
							"<?php echo _('anglikanisch'); ?>": anglicanLayer,
							"<?php echo _('evangelisch'); ?>": protestantLayer,
							"<?php echo _('freikirchlich'); ?>": freeChurchesLayer,
							"<?php echo _('katholisch'); ?>": catholicLayer,
							"<?php echo _('andere Konfession'); ?>": othersLayer,
							"<?php echo $websites['web']; ?>": webLayer,
							"<?php echo $websites['blog']; ?>": blogLayer,
							"RSS": rssLayer,
							"Facebook": facebookLayer,
							"Flickr": flickrLayer,
							"Google+": googleLayer,
							"Instagram": instagramLayer,
							"Soundcloud": soundcloudLayer,
							"Twitter": twitterLayer,
							"Vimeo": vimeoLayer,
							"YouTube": youtubeLayer,
						}
						L.control.layers(layers).addTo(map);
						// put markers into a group to
						var group = L.featureGroup(markerArray).addTo(map);
						map.fitBounds(group.getBounds());
					})
		};
	</script>
	
	<?php include_once 'includes/footer.php'; ?>
</body>
</html>