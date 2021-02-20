const cleanCss = require('gulp-clean-css');
const concat = require('gulp-concat');
const del = require('del');
const gulp = require('gulp');
const minify = require('gulp-minify');
const rename = require('gulp-rename');
const rev = require('gulp-rev');
const sass = require('gulp-sass');

const { parallel } = require('gulp');

const assetsRoot = 'public/assets';
const resourcesRoot = 'resources';

function copyCSSFromLibraries () {
  del([assetsRoot + '/lib/*.css']);
  return gulp.src(
    [
      'node_modules/leaflet/dist/leaflet.css',
      'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
      'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css'
    ])
    .pipe(concat('leaflet-bundle.css'))
    .pipe(cleanCss())
    .pipe(rev())
    .pipe(gulp.dest(assetsRoot + '/lib'))
    .pipe(rename({
      dirname: 'lib'
    }))
    .pipe(rev.manifest('assets/rev-manifest.json', {
      merge: true,
      base: 'assets'
    }))
    .pipe(gulp.dest(assetsRoot));
}

function copyImagesFromLibraries () {
  del([assetsRoot + '/lib/images/*.png']);
  return gulp.src(
    [
      'node_modules/leaflet/dist/images/layers.png',
      'node_modules/leaflet/dist/images/layers-2x.png'
    ])
    .pipe(gulp.dest(assetsRoot + '/lib/images'))
}

function copyJavaScriptLibraries () {
  del([assetsRoot + '/lib/*.js']);
  return gulp.src(
    [
      'node_modules/bootstrap/dist/js/bootstrap.min.js',
      'node_modules/jquery/dist/jquery.min.js',
      'node_modules/highcharts/highcharts.js',
      'node_modules/leaflet/dist/leaflet.js',
      'node_modules/leaflet.markercluster/dist/leaflet.markercluster.js',
      'node_modules/tablesorter/dist/js/jquery.tablesorter.min.js'
    ])
    .pipe(rev())
    .pipe(gulp.dest(assetsRoot + '/lib'))
    .pipe(rename({
      dirname: 'lib'
    }))
    .pipe(rev.manifest(assetsRoot + '/rev-manifest.json', {
      merge: true,
      base: assetsRoot
    }))
    .pipe(gulp.dest(assetsRoot));
}

function minifyCSS () {
  del([assetsRoot + '/css/*.css']);
  return gulp.src([resourcesRoot + '/css/*.scss'], { base: resourcesRoot + '/css' })
    .pipe(sass({
      outputStyle: 'compressed'
    }).on(
      'error',
      sass.logError
    ))
    .pipe(rev())
    .pipe(gulp.dest(assetsRoot + '/css'))
    .pipe(rename({
      dirname: 'css'
    }))
    .pipe(rev.manifest(assetsRoot + '/rev-manifest.json', {
      merge: true,
      base: assetsRoot
    }))
    .pipe(gulp.dest(assetsRoot));
}

function minifyJavaScript () {
  del([assetsRoot + '/js/*.js']);
  return gulp.src([resourcesRoot + '/js/*.js'], { base: resourcesRoot + '/js' })
    .pipe(minify({
      ext: {
        min: '.js'
      },
      noSource: true
    }))
    .pipe(rev())
    .pipe(gulp.dest(assetsRoot + '/js'))
    .pipe(rename({
      dirname: 'js'
    }))
    .pipe(rev.manifest(assetsRoot + '/rev-manifest.json', {
      merge: true,
      base: assetsRoot
    }))
    .pipe(gulp.dest(assetsRoot));
}

function watchCSS () {
  return gulp.watch(resourcesRoot + '/css/*.scss', minifyCSS);
}

function watchJavaScript () {
  return gulp.watch(resourcesRoot + '/js/*.js', minifyJavaScript);
}

exports.copy = parallel(copyCSSFromLibraries, copyImagesFromLibraries, copyJavaScriptLibraries);
exports.default = parallel(minifyCSS, minifyJavaScript);
exports.watch = parallel(watchCSS, watchJavaScript);
