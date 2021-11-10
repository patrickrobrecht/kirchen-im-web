/* eslint no-unused-vars: ["error", { "varsIgnorePattern": "Map" }] */
/* global L, fetch */
function Map (translations) {
  this.translations = translations;
  this.load = function (mapId, baseUrl, dataPath, language) {
    const t = this.translations;
    const detailsUrlPrefix = baseUrl + '/' + language;

    // the layers
    const allLayer = new L.LayerGroup();
    const oldCatholicLayer = new L.LayerGroup();
    const anglicanLayer = new L.LayerGroup();
    const protestantLayer = new L.LayerGroup();
    const freeChurchesLayer = new L.LayerGroup();
    const catholicLayer = new L.LayerGroup();
    const othersLayer = new L.LayerGroup();

    const webLayer = new L.LayerGroup();
    const blogLayer = new L.LayerGroup();
    const podcastLayer = new L.LayerGroup();
    const rssLayer = new L.LayerGroup();
    const facebookLayer = new L.LayerGroup();
    const flickrLayer = new L.LayerGroup();
    const instagramLayer = new L.LayerGroup();
    const soundcloudLayer = new L.LayerGroup();
    const twitterLayer = new L.LayerGroup();
    const vimeoLayer = new L.LayerGroup();
    const youtubeLayer = new L.LayerGroup();

    // the icons
    const oldCatholicIcon = L.icon({ iconUrl: baseUrl + '/images/markers/orange.png' });
    const anglicanIcon = L.icon({ iconUrl: baseUrl + '/images/markers/green.png' });
    const protestantIcon = L.icon({ iconUrl: baseUrl + '/images/markers/purple.png' });
    const freeChurchesIcon = L.icon({ iconUrl: baseUrl + '/images/markers/blue.png' });
    const catholicIcon = L.icon({ iconUrl: baseUrl + '/images/markers/yellow.png' });
    const othersIcon = L.icon({ iconUrl: baseUrl + '/images/markers/red.png' });

    // Create a map in the container with the given id, set the view to a given place and zoom.
    const map = L.map(mapId, {
      center: L.latLng(50, 10),
      zoom: 6
    });
    // Add an OpenStreetMap tile layer.
    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> (CC BY-SA)'
    }).addTo(map);
    const markerCluster = L.markerClusterGroup({
      maxClusterRadius: 80,
      chunkedLoading: true
    });

    fetch(baseUrl + dataPath).then(response => {
      response.json().then(entries => {
        let icon, denominationLayer, content, marker;
        for (const entry of entries) {
          switch (entry.denomination) {
            case 'alt-katholisch':
              denominationLayer = oldCatholicLayer;
              icon = oldCatholicIcon;
              break;
            case 'anglikanisch':
              denominationLayer = anglicanLayer;
              icon = anglicanIcon;
              break;
            case 'evangelisch':
              denominationLayer = protestantLayer;
              icon = protestantIcon;
              break;
            case 'freikirchlich':
              denominationLayer = freeChurchesLayer;
              icon = freeChurchesIcon;
              break;
            case 'katholisch':
              denominationLayer = catholicLayer;
              icon = catholicIcon;
              break;
            default:
              denominationLayer = othersLayer;
              icon = othersIcon;
              break;
          }

          if (entry.lat && entry.lon && entry.name && icon) {
            marker = L.marker([entry.lat, entry.lon], { title: entry.name, icon: icon });

            // Add the marker to the cluster.
            markerCluster.addLayer(marker);

            // Add to the layers for the denominations.
            marker.addTo(allLayer);
            marker.addTo(denominationLayer);

            // Build the popup for the marker.
            content = '<strong><a href="' + detailsUrlPrefix + '/details/' + entry.slug + '/">' + entry.name + '</a></strong><br>' + entry.street + ', ' + entry.postalCode + ' ' + entry.city + '<br><ul>';

            // Add to the layers for the social networks.
            if (entry.web) {
              marker.addTo(webLayer);
              content = content + '<li><a href="' + entry.web + '">' + t.web + '</a></li>';
            }
            if (entry.blog) {
              marker.addTo(blogLayer);
              content = content + '<li><a href="' + entry.blog + '">' + t.blog + '</a></li>';
            }
            if (entry.podcast) {
              marker.addTo(podcastLayer);
              content = content + '<li><a href="' + entry.podcast + '">' + t.podcast + '</a></li>';
            }
            if (entry.rss) {
              marker.addTo(rssLayer);
              content = content + '<li><a href="' + entry.rss + '">RSS</a></li>';
            }
            if (entry.facebook) {
              marker.addTo(facebookLayer);
              content = content + '<li><a href="' + entry.facebook + '">Facebook</a></li>';
            }
            if (entry.flickr) {
              marker.addTo(flickrLayer);
              content = content + '<li><a href="' + entry.flickr + '">Flickr</a></li>';
            }
            if (entry.instagram) {
              marker.addTo(instagramLayer);
              content = content + '<li><a href="' + entry.instagram + '">Instagram</a></li>';
            }
            if (entry.soundcloud) {
              marker.addTo(soundcloudLayer);
              content = content + '<li><a href="' + entry.soundcloud + '">Soundcloud</a></li>';
            }
            if (entry.twitter) {
              marker.addTo(twitterLayer);
              content = content + '<li><a href="' + entry.twitter + '">Twitter</a></li>';
            }
            if (entry.vimeo) {
              marker.addTo(vimeoLayer);
              content = content + '<li><a href="' + entry.vimeo + '">Vimeo</a></li>';
            }
            if (entry.youtube) {
              marker.addTo(youtubeLayer);
              content = content + '<li><a href="' + entry.youtube + '">YouTube</a></li>';
            }
            marker.bindPopup(content + '</ul>');
          } else {
            console.error('Invalid entry ', entry);
          }
        }

        // Add control for the layers.
        const layers = {};
        layers[t.all] = allLayer;
        layers[t.oldCatholic] = oldCatholicLayer;
        layers[t.anglican] = anglicanLayer;
        layers[t.protestant] = protestantLayer;
        layers[t.freeChurches] = freeChurchesLayer;
        layers[t.catholic] = catholicLayer;
        layers[t.others] = othersLayer;
        layers[t.web] = webLayer;
        layers[t.blog] = blogLayer;
        layers[t.podcast] = podcastLayer;
        layers.RSS = rssLayer;
        layers.Facebook = facebookLayer;
        layers.Flickr = flickrLayer;
        layers.Instagram = instagramLayer;
        layers.Soundcloud = soundcloudLayer;
        layers.Twitter = twitterLayer;
        layers.Vimeo = vimeoLayer;
        layers.YouTube = youtubeLayer;
        L.control.layers(layers).addTo(map);

        // Add cluster.
        map.addLayer(markerCluster);
      });
    });
  }
}
