const cleanCss = require('gulp-clean-css');
const concat = require('gulp-concat');
const del = require('del');
const gulp = require('gulp');
const minify = require('gulp-minify');
const rename = require("gulp-rename");
const rev = require('gulp-rev');
const sass = require('gulp-sass');

const {parallel} = require('gulp');

function copyCSSFromLibraries() {
    del(['assets/lib/*.css']);
    return gulp.src(
        [
            'node_modules/leaflet/dist/leaflet.css',
            'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
            'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css'
        ])
        .pipe(concat('leaflet-bundle.css'))
        .pipe(cleanCss())
        .pipe(rev())
        .pipe(gulp.dest('assets/lib'))
        .pipe(rename({
            dirname: 'lib'
        }))
        .pipe(rev.manifest('assets/rev-manifest.json', {
            merge: true,
            base: 'assets'
        }))
        .pipe(gulp.dest('assets'));
}

function copyImagesFromLibraries() {
    del(['assets/lib/images/*.png']);
    return gulp.src(
        [
            'node_modules/leaflet/dist/images/layers.png',
            'node_modules/leaflet/dist/images/layers-2x.png'
        ])
        .pipe(gulp.dest('assets/lib/images'))
}

function copyJavaScriptLibraries() {
    del(['assets/lib/*.js']);
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
        .pipe(gulp.dest('assets/lib'))
        .pipe(rename({
            dirname: 'lib'
        }))
        .pipe(rev.manifest('assets/rev-manifest.json', {
            merge: true,
            base: 'assets'
        }))
        .pipe(gulp.dest('assets'));
}

function minifyCSS() {
    del(['assets/css/*.css']);
    return gulp.src(['theme/css/*.scss'], {base: 'theme/css'})
        .pipe(sass({
            outputStyle: 'compressed'
        }).on(
            'error',
            sass.logError
        ))
        .pipe(rev())
        .pipe(gulp.dest('assets/css'))
        .pipe(rename({
            dirname: 'css'
        }))
        .pipe(rev.manifest('assets/rev-manifest.json', {
            merge: true,
            base: 'assets'
        }))
        .pipe(gulp.dest('assets'));
}

function minifyJavaScript() {
    del(['assets/*.js']);
    return gulp.src(['theme/js/*.js'], {base: 'theme/js'})
        .pipe(minify({
            ext: {
                min: '.js'
            },
            noSource: true
        }))
        .pipe(rev())
        .pipe(gulp.dest('assets/js'))
        .pipe(rename({
            dirname: 'js'
        }))
        .pipe(rev.manifest('assets/rev-manifest.json', {
            merge: true,
            base: 'assets'
        }))
        .pipe(gulp.dest('assets'));
}

function watchCSS() {
    return gulp.watch('theme/css/*.scss', minifyCSS);
}

function watchJavaScript() {
    return gulp.watch('theme/js/*.js', minifyJavaScript);
}

exports.copy = parallel(copyCSSFromLibraries, copyImagesFromLibraries, copyJavaScriptLibraries);
exports.default = parallel(minifyCSS, minifyJavaScript);
exports.watch = parallel(watchCSS, watchJavaScript);
