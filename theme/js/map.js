/* eslint no-unused-vars: ["error", { "varsIgnorePattern": "Map" }] */
function Map (translations) {
  this.translations = translations;
  this.load = function (mapId, baseUrl, dataPath, language) {
    const t = this.translations;
    const detailsUrlPrefix = baseUrl + '/' + language;
    const markerArray = [];
    // the layers
    /* global L */
    /* eslint no-undef: "error" */
    const allLayer = new L.LayerGroup();
    const oldCatholicLayer = new L.LayerGroup();
    const anglicanLayer = new L.LayerGroup();
    const protestantLayer = new L.LayerGroup();
    const freeChurchesLayer = new L.LayerGroup();
    const catholicLayer = new L.LayerGroup();
    const othersLayer = new L.LayerGroup();
    const webLayer = new L.LayerGroup();
    const blogLayer = new L.LayerGroup();
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
    const map = L.map(mapId, { center: L.latLng(50, 10), zoom: 6 });
    // Add an OpenStreetMap tile layer.
    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> (CC BY-SA)'
    }).addTo(map);
    const markerCluster = L.markerClusterGroup({ maxClusterRadius: 80, chunkedLoading: true });
    /* global $ */
    /* eslint no-undef: "error" */
    $.getJSON(baseUrl + dataPath,
      function (data) {
        let title, denomination, icon, denominationLayer, content, thisMarker;
        $.each(data,
          function (i, v) {
            // Read the JSON data.
            title = v.name;
            denomination = v.denomination;
            if (denomination === 'alt-katholisch') {
              denominationLayer = oldCatholicLayer;
              icon = oldCatholicIcon;
            } else if (denomination === 'anglikanisch') {
              denominationLayer = anglicanLayer;
              icon = anglicanIcon;
            } else if (denomination === 'evangelisch') {
              denominationLayer = protestantLayer;
              icon = protestantIcon;
            } else if (denomination === 'freikirchlich') {
              denominationLayer = freeChurchesLayer;
              icon = freeChurchesIcon;
            } else if (denomination === 'katholisch') {
              denominationLayer = catholicLayer;
              icon = catholicIcon;
            } else {
              denominationLayer = othersLayer;
              icon = othersIcon;
            }

            if (v.lat && v.lon && title && icon) {
              thisMarker = L.marker([v.lat, v.lon], { title: title, icon: icon });

              // Push the marker to the Array which shall be displayed on the map.
              markerArray.push(thisMarker);
              markerCluster.addLayer(thisMarker);

              // Add to the layers for the denominations.
              thisMarker.addTo(allLayer);
              thisMarker.addTo(denominationLayer);

              // Build the popup for the marker.
              content = '<strong><a href="' + detailsUrlPrefix + '/details/' + v.slug + '/">' + title + '</a></strong><br>' + v.street + ', ' + v.postalCode + ' ' + v.city + '<br><ul>';

              // Add to the layers for the social networks.
              if (v.web) {
                thisMarker.addTo(webLayer);
                content = content + '<li><a href="' + v.web + '">' + t.web + '</a></li>';
              }
              if (v.blog) {
                thisMarker.addTo(blogLayer);
                content = content + '<li><a href="' + v.blog + '">' + t.blog + '</a></li>';
              }
              if (v.rss) {
                thisMarker.addTo(rssLayer);
                content = content + '<li><a href="' + v.rss + '">RSS</a></li>';
              }
              if (v.facebook) {
                thisMarker.addTo(facebookLayer);
                content = content + '<li><a href="' + v.facebook + '">Facebook</a></li>';
              }
              if (v.flickr) {
                thisMarker.addTo(flickrLayer);
                content = content + '<li><a href="' + v.flickr + '">Flickr</a></li>';
              }
              if (v.instagram) {
                thisMarker.addTo(instagramLayer);
                content = content + '<li><a href="' + v.instagram + '">Instagram</a></li>';
              }
              if (v.soundcloud) {
                thisMarker.addTo(soundcloudLayer);
                content = content + '<li><a href="' + v.soundcloud + '">Soundcloud</a></li>';
              }
              if (v.twitter) {
                thisMarker.addTo(twitterLayer);
                content = content + '<li><a href="' + v.twitter + '">Twitter</a></li>';
              }
              if (v.vimeo) {
                thisMarker.addTo(vimeoLayer);
                content = content + '<li><a href="' + v.vimeo + '">Vimeo</a></li>';
              }
              if (v.youtube) {
                thisMarker.addTo(youtubeLayer);
                content = content + '<li><a href="' + v.youtube + '">YouTube</a></li>';
              }

              thisMarker.bindPopup(content + '</ul>');
            } else {
              console.error('Problem with entry ' + v.id + ' ' + title);
            }
          });

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
      })
  }
}
