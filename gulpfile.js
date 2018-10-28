const del = require('del');
const gulp = require('gulp');
const minify = require('gulp-minify');
const rev = require('gulp-rev');
const sass = require('gulp-sass');

const {parallel} = require('gulp');

function minifyCSS() {
    del(['assets/*.css']);
    return gulp.src(['public/css/*.scss'])
        .pipe(sass({
            outputStyle: 'compressed'
        }).on(
            'error',
            sass.logError
        ))
        .pipe(rev())
        .pipe(gulp.dest('assets'))
        .pipe(rev.manifest('assets/rev-manifest.json', {
            merge: true,
            base: 'assets'
        }))
        .pipe(gulp.dest('assets'));
}

function minifyJavaScript() {
    del(['assets/*.js']);
    return gulp.src(['public/js/*.js', '!public/js/*.min.js'])
        .pipe(minify({
            ext: {
                min: '.js'
            },
            noSource: true
        }))
        .pipe(rev())
        .pipe(gulp.dest('assets'))
        .pipe(rev.manifest('assets/rev-manifest.json', {
            merge: true,
            base: 'assets'
        }))
        .pipe(gulp.dest('assets'));
}

function watchCSS() {
    return gulp.watch('public/css/*.scss', minifyCSS);
}

function watchJavaScript() {
    return gulp.watch('public/js/*.js', minifyJavaScript);
}

exports.default = parallel(minifyCSS, minifyJavaScript);
exports.watch = parallel(watchCSS, watchJavaScript);
