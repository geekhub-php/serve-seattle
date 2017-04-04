// var gulp = require('gulp'),
//     less = require('gulp-less'),
//     concatJs = require('gulp-concat'),
//     minifyJs = require('gulp-uglify'),
//
// gulp.task('less', function() {
//     return gulp.src([
//         'web-src/less/style.less'
//     ])
//         .pipe(less({compress: true}))
//         .pipe(gulp.dest('web/css/'));
// });
//
// gulp.task('css', function() {
//     return gulp.src([
//         'bower_components/bootstrap/dist/css/bootstrap.css',
//         'bower_components/font-awesome/css/font-awesome.css',
//         'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
//         'web-src/css/**/*.css'
//     ])
//         .pipe(less({compress: true}))
//         .pipe(gulp.dest('web/css/'));
// });
//
// gulp.task('fonts', function () {
//     return gulp.src([
//         'bower_components/bootstrap/fonts/*',
//         'bower_components/font-awesome/fonts/*',
//         'web-src/fonts/*'
//     ])
//         .pipe(gulp.dest('web/fonts/'))
// });
//
// gulp.task('images', function () {
//     return gulp.src([
//         'web-src/images/**/*'
//     ])
//         .pipe(gulp.dest('web/images/'))
// });
//
// gulp.task('pages-js', function() {
//     return gulp.src([
//         'bower_components/jquery/dist/jquery.js',
//         'bower_components/bootstrap/dist/js/bootstrap.js',
//         'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
//         'web-src/js/**/*.js'
//     ])
//         .pipe(minifyJs())
//         .pipe(gulp.dest('web/js/'));
// });
//
// gulp.task('clean', function () {
//     return gulp.src(['web/css/*', 'web/js/*', 'web/fonts/*', 'font-awesome', 'web/images/*'])
//         .pipe(clean());
// });
//
// gulp.task('default', ['clean'], function () {
//     var tasks = ['less', 'css', 'fonts', 'pages-js', 'images'];
//     tasks.forEach(function (val) {
//         gulp.start(val);
//     });
// });
//
// gulp.task('watch', function () {
//     var less = gulp.watch('web-src/less/*.less', ['less']),
//         js = gulp.watch('web-src/js/*.js', ['pages-js']);
// });

const gulp = require("gulp");
const nib = require("nib");
const stylus = require("gulp-stylus");
const cssmin = require("gulp-cssmin");
const rename = require("gulp-rename");
const plumber = require("gulp-plumber");
const bootstrap = require("bootstrap-styl");
const babelify = require('babelify');
const source = require('vinyl-source-stream');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const browserify = require('browserify');

gulp.task("default", ["image", "js", "stylus", "watch"]);

//Styles_task

gulp.task("stylus", function () {
    return gulp
        .src("./web-src/styl/index.styl")
        .pipe(plumber())
        .pipe( stylus({
            compress: true,
            use: [nib(), bootstrap()]
        }))
        .pipe(cssmin())
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(gulp.dest('./web/css/'));
});

//Image_compress_task

gulp.task("image", function () {
    gulp.src('./web-src/img/*')
        .pipe(imagemin())
        .pipe(gulp.dest('./web/img/'));
});

//Compiling_JS_task

gulp.task('js', function() {
    gulp.src('./web-src/scripts/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('./web/js/'))
});

gulp.task('clean', function () {
    return gulp.src(['web/css/*', 'web/js/*', 'web/fonts/*', 'font-awesome', 'web/img/*'])
        .pipe(clean());
});

//Watch_task

gulp.task("watch", function () {
    gulp.watch('./web-src/**/*.styl', ['stylus']);
    gulp.watch(['web-src/scripts/*.js'], ['js']);
});