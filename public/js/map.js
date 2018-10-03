function Map(translations) {
    this.translations = translations;
    this.load = function (mapId, url) {
        var t = this.translations;
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
        var oldCatholicIcon = L.icon({iconUrl: '../../images/markers/orange.png'});
        var anglicanIcon = L.icon({iconUrl: '../../images/markers/green.png'});
        var protestantIcon = L.icon({iconUrl: '../../images/markers/purple.png'});
        var freeChurchesIcon = L.icon({iconUrl: '../../images/markers/blue.png'});
        var catholicIcon = L.icon({iconUrl: '../../images/markers/yellow.png'});
        var othersIcon = L.icon({iconUrl: '../../images/markers/red.png'});

        'use strict'; // Strict mode makes the browse mourn, if bad practise is used
        // create a map in the "map" div, set the view to a given place and zoom
        map = L.map(mapId, {center: L.latLng(50, 10), zoom: 6});
        // add an OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> (CC BY-SA)'
        }).addTo(map);
        var markerCluster = L.markerClusterGroup({maxClusterRadius: 80, chunkedLoading: true});
        $.getJSON(url,
            function(data) {
                var title, denomination, icon, denominationLayer, content, thisMarker;
                $.each(data,
                    function(i, v) {
                        // read the JSON data
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
                            thisMarker = L.marker([v.lat, v.lon], {title: title, icon: icon});

                            // Push the marker to the Array which shall be displayed on the map
                            markerArray.push(thisMarker);
                            markerCluster.addLayer(thisMarker);

                            // add to the layers for the denominations
                            thisMarker.addTo(allLayer);
                            thisMarker.addTo(denominationLayer);

                            // Build the popup for the marker
                            content = '<strong><a href="/{{ languageSlug }}/details/' + v.id + '/">' + title + '</a></strong><br>' + v.street + ', ' + v.postalCode + ' ' + v.city + '<br><ul>';

                            // add to the layers for the social networks
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
                            if (v.googlePlus) {
                                thisMarker.addTo(googleLayer);
                                content = content + '<li><a href="' + v.googlePlus + '">Google+</a></li>';
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
                // add control for the layers
                var layers = {};
                layers[t.all] = allLayer;
                layers[t.oldCatholic] = oldCatholicLayer;
                layers[t.anglican] = anglicanLayer;
                layers[t.protestant] = protestantLayer;
                layers[t.freeChurches] = freeChurchesLayer;
                layers[t.catholic] = catholicLayer;
                layers[t.others] = othersLayer;
                layers[t.web] = webLayer;
                layers[t.blog] = blogLayer;
                layers["RSS"] = rssLayer;
                layers["Facebook"] = facebookLayer;
                layers["Flickr"] = flickrLayer;
                layers["Google+"] = googleLayer;
                layers["Instagram"] = instagramLayer;
                layers["Soundcloud"] = soundcloudLayer;
                layers["Twitter"] = twitterLayer;
                layers["Vimeo"] = vimeoLayer;
                layers["YouTube"] = youtubeLayer;
                L.control.layers(layers).addTo(map);

                // Add cluster.
                map.addLayer(markerCluster);
            })
    }
}
