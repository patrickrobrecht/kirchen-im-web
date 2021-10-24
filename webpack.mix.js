const mix = require('laravel-mix');
const convertToFileHash = require('laravel-mix-make-file-hash');

mix.setPublicPath('public/');

mix.sourceMaps(true, 'source-map');

// Compile custom style.
mix.sass('resources/css/style.scss', 'public/assets/css').options({
  processCssUrls: false
});

// Compile JavaScript files.
mix.js('resources/js/add.js', 'public/assets/js');
mix.js('resources/js/map.js', 'public/assets/js');

// Copy required CSS, JavaScript and images from libraries.
mix.styles([
  'node_modules/leaflet/dist/leaflet.css',
  'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
  'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css'
], 'public/assets/lib/leaflet-bundle.css');

const javaScriptLibraries = [
  'node_modules/bootstrap/dist/js/bootstrap.min.js',
  'node_modules/jquery/dist/jquery.min.js',
  'node_modules/highcharts/highcharts.js',
  'node_modules/leaflet/dist/leaflet.js',
  'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js',
  'node_modules/tablesorter/dist/js/jquery.tablesorter.min.js'
];
javaScriptLibraries.forEach(
  file => mix.copy(file, 'public/assets/lib')
);

const imagesFromLibraries = [
  'node_modules/leaflet/dist/images/layers.png',
  'node_modules/leaflet/dist/images/layers-2x.png'
];
imagesFromLibraries.forEach(
  file => mix.copy(file, 'public/assets/lib/images')
)

if (mix.inProduction()) {
  mix.disableNotifications();

  // Setup hash-based file names for the production environment.
  mix.version();
  mix.then(() => {
    convertToFileHash({
      publicPath: 'public/',
      manifestFilePath: 'public/assets/mix-manifest.json'
    });
  });
}
