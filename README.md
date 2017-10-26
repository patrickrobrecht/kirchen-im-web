![Screenshot](https://kirchen-im-web.de/images/screenshot.png)

# [kirchen-im-web.de](https://kirchen-im-web.de/en/) - Directory of Churchy Websites and Social Network Pages 

We want to show that many parishes, regional churches and dioceses are do a good job in public relations 
    and present those with their websites and social network pages 
    [at a map](https://kirchen-im-web.de/en/map/) and 
    [tabularly](https://kirchen-im-web.de/en/search/).
kirchen-im-web.de also compares the follower numbers of the
    [social media pages](https://kirchen-im-web.de/en/comparison/). 

Our Project is oecumenic, i. e. regardless whether Protestants, Catholic or free church - all can participate. 
You can simply add your church using our [form](https://kirchen-im-web.de/en/add/).

The latest development version is online at [beta.kirchen-im-web.de](https://beta.kirchen-im-web.de/en/).

## Features
* [map](https://kirchen-im-web.de/en/map/)
    with all churches which can be filtered according to denomination and network
* [table](https://kirchen-im-web.de/en/search/)
    with filtering according to name, postal code, city, country, denomination, type, network
* [details page](https://kirchen-im-web.de/en/details/1/)
    for every church showing all websites and networks, map and church hierarchy
* form to [add new churches](https://kirchen-im-web.de/en/add/)
* [statistics](https://kirchen-im-web.de/en/statistics/)
    and downloadable [open data](https://kirchen-im-web.de/en/data/)
* available in English and German

## API Documentation

kirchen-im-web.de offers the data via a public API at `https://kirchen-im-web.de/api/` which returns all the data on the selected churches in JSON format. Even if there is only one result for the query, the data is returned as a list.

Per default, the whole data is returned. Parameters can be used to reduce the number of entries which are returned.

Example: A call to `https://kirchen-im-web.de/api/churches/?name=test` only returns data of the churches whose name contains the string `test`.

* `name`: the substring of the name
* `postalCode`: the postal code
* `city`: the substring of the city
* `country`: the country code (e. g. `DE` for Germany).
	Possible values: see `$countries` variable in the configuration file
* `denomination`: the denomination. 
	Possible values: see `$denominations` variable in the configuration file
* `type`: the type. 
	Possible values: see `$types` variable in the configuration file
* `hasWebsiteType`: the website type.
	Possible values: see `$websites` variable in the configuration file

## Used programming languages and libraries
* [HTML5, CSS 3.0](https://www.w3.org/standards/webdesign/htmlcss)
* [PHP](https://secure.php.net/) with libraries
    [Slim](https://www.slimframework.com/) and 
    [Twig](https://twig.symfony.com/)
* JavaScript, especially the libraries 
    [jQuery](https://jquery.com/),
    [Highcharts](http://www.highcharts.com/),
    [Leaflet](http://leafletjs.com/) and 
    [Tablesorter](http://tablesorter.com/)


# [kirchen-im-web.de](https://kirchen-im-web.de/de/) - Verzeichnis kirchlicher Web- und Social-Media-Auftritte

Wir wollen zeigen, dass viele Kirchengemeinden, Landeskirchen und Bistümer auch im Web 
    gute Öffentlichtkeitsarbeit machen und stellen diese mit ihren Web- und Social-Media-Auftritten 
    [in einer Karte](https://kirchen-im-web.de/de/karte/) und 
    [tabellarisch](https://kirchen-im-web.de/de/suche/) dar.
kirchen-im-web.de vergleicht auch die Follower-Zahlen der
    [Social-Media-Auftritte](https://kirchen-im-web.de/de/vergleich/).

Unser Projekt ist überkonfessionell, d. h. egal ob evangelisch, katholisch oder freikirchlich - alle können mitmachen. 
Sie können Ihre Gemeinde einfach über unser 
    [Formular](https://kirchen-im-web.de/de/eintragen/) hinzufügen.

Die aktuelle Entwicklungsversion gibt es unter 
    [beta.kirchen-im-web.de](https://beta.kirchen-im-web.de/de/).

## Funktionen
* [Karte](https://kirchen-im-web.de/de/karte/)
    mit allen Gemeinden, die nach Konfession und Netzwerk gefiltert werden kann
* [Tabelle](https://kirchen-im-web.de/de/suche/)
    mit Filter nach Name, PLZ, Stadt, Land, Konfession, Gemeindetyp, Netzwerk
* [Detailseite](https://kirchen-im-web.de/de/details/1/)
    für jede Gemeinde, die alle Web- und Social-Media-Auftritte, eine Karte und die Hierarchie anzeigt
* Formular zum [Hinzufügen neuer Gemeinden](https://kirchen-im-web.de/de/eintragen/)
* [Statistik](https://kirchen-im-web.de/de/statistik/)
    und herunterladbare [offene Daten](https://kirchen-im-web.de/de/daten/)
* verfügbar in Deutsch und Englisch
