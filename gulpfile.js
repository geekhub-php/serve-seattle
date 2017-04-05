const gulp = require("gulp");
const nib = require("nib");
const stylus = require("gulp-stylus");
const cssmin = require("gulp-cssmin");
const rename = require("gulp-rename");
const plumber = require("gulp-plumber");
const bootstrap = require("bootstrap-styl");
const source = require('vinyl-source-stream');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const browserify = require('browserify');

gulp.task("default", ["css", "fonts", "image", "js", "stylus"]);

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

gulp.task('css', function() {
    return gulp.src([
        'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
        'node_modules/fullcalendar/dist/fullcalendar.min.css',
        'node_modules/fullcalendar/dist/fullcalendar.print.min.css',
        'web-src/css/**/*.css'
    ])
        .pipe(gulp.dest('web/css/'));
});
//Image_compress_task

gulp.task("image", function () {
    gulp.src('./web-src/img/*')
        .pipe(imagemin())
        .pipe(gulp.dest('./web/img/'));
});

//Compiling_JS_task

gulp.task('js', function() {
    gulp.src([
        'bower_components/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/moment/min/moment.min.js',
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/jquery-validation/dist/jquery.validate.min.js',
        'node_modules/fullcalendar/dist/fullcalendar.min.js',
        'node_modules/fullcalendar/dist/gcal.min.js',
        './web-src/scripts/*.js'
    ])
        .pipe(uglify())
        .pipe(gulp.dest('./web/js/'))
});

gulp.task('fonts', function () {
    return gulp.src([
        'bower_components/font-awesome/**'
    ])
        .pipe(gulp.dest('./web/fonts/'))
});

//Watch_task

gulp.task("watch", function () {
    gulp.watch('./web-src/**/*.styl', ['stylus']);
});