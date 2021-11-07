const del = require('del');
const mix = require('laravel-mix');

del.sync([
  'public/assets'
]);

mix.setPublicPath('public/assets');

mix.sourceMaps(true, 'source-map');

// Compile custom style.
mix.sass('resources/css/style.scss', 'public/assets/css').options({
  processCssUrls: false
});

// Compile JavaScript files.
const javaScriptFiles = [
  'add.js',
  'table.js',
  'map.js'
];
javaScriptFiles.forEach(file => mix.copy('resources/js/' + file, 'public/assets/js'));

// Copy required CSS, JavaScript and images from libraries.
const cssFilesForLeaflet = [
  'node_modules/leaflet/dist/leaflet.css',
  'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
  'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css'
];
mix.styles(cssFilesForLeaflet, 'public/assets/lib/leaflet-bundle.css');

const javaScriptLibraries = [
  'node_modules/bootstrap/dist/js/bootstrap.min.js',
  'node_modules/highcharts/highcharts.js',
  'node_modules/leaflet/dist/leaflet.js',
  'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js',
  'node_modules/tablesort/dist/tablesort.min.js'
];
javaScriptLibraries.forEach(file => mix.copy(file, 'public/assets/lib'));

mix.copy('node_modules/leaflet/dist/images/layers*.png', 'public/assets/lib/images');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/assets/webfonts');

// Setup hash-based file names for the production environment.
if (mix.inProduction()) {
  mix.version();
  mix.then(() => {
    const convertToFileHash = require('laravel-mix-make-file-hash');
    convertToFileHash({
      publicPath: 'public/assets/',
      manifestFilePath: 'public/assets/mix-manifest.json',
      blacklist: ['webfonts', 'lib/images/**']
    });
  });
}
