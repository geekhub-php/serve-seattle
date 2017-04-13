const gulp = require("gulp");
const nib = require("nib");
const stylus = require("gulp-stylus");
const cssmin = require("gulp-cssmin");
const plumber = require("gulp-plumber");
const bootstrap = require("bootstrap-styl");
const uglify = require('gulp-uglify');
const clean = require('gulp-clean');
const concat = require('gulp-concat');
const imagemin = require('gulp-imagemin');

gulp.task("default", ["fonts", "calendar_print", "image", "js", "stylus"]);

//Styles_task

gulp.task("stylus", function () {
    return gulp
        .src([
            './web-src/styl/index.styl',
            'web-src/css/**/*.css',
            'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
            'node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
            'node_modules/fullcalendar/dist/fullcalendar.min.css'
        ])
        .pipe(plumber())
        .pipe( stylus({
            compress: true,
            use: [nib(), bootstrap()]
        }))
        .pipe(cssmin())
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest('./web/css/'));
});

gulp.task('calendar_print', function() {
    return gulp.src([
        'node_modules/fullcalendar/dist/fullcalendar.print.min.css'
    ])
        .pipe(gulp.dest('./web/css/'));
});
// Image_compress_task

gulp.task("image", function () {
    gulp.src('./web-src/img/*')
        .pipe(imagemin())
        .pipe(gulp.dest('./web/img/'));
});

//Compiling_JS_task

gulp.task('js', function() {
    gulp.src([
        'node_modules/moment/min/moment.min.js',
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/fullcalendar/dist/fullcalendar.min.js',
        'node_modules/fullcalendar/dist/gcal.min.js',
        'node_modules/jquery-validation/dist/jquery.validate.min.js',
        'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
        'node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'
    ])
        .pipe(uglify())
        .pipe(concat('app.min.js'))
        .pipe(gulp.dest('./web/js/'))
});

gulp.task('fonts', function () {
    return gulp.src([
        'node_modules/font-awesome/css/*',
        'node_modules/font-awesome/fonts/*',
        'node_modules/bootstrap/dist/fonts/*'
    ])
        .pipe(gulp.dest('./web/fonts/'))
});

gulp.task('clean', function () {
    return gulp.src(['./web/css/*', './web/js/*', './web/fonts/*', './web/img/*'])
        .pipe(clean());
});

//Watch_task

gulp.task("watch", function () {
    gulp.watch('./web-src/**/*.styl', ['stylus']);
});
