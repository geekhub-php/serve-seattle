var gulp = require('gulp'),
    less = require('gulp-less'),
    concatJs = require('gulp-concat'),
    minifyJs = require('gulp-uglify'),
    clean = require('gulp-clean');

gulp.task('less', function() {
    return gulp.src([
        'web-src/less/style.less'
    ])
        .pipe(less({compress: true}))
        .pipe(gulp.dest('web/css/'));
});

gulp.task('css', function() {
    return gulp.src([
        'bower_components/bootstrap/dist/css/bootstrap.css',
        'bower_components/font-awesome/css/font-awesome.css',
        'web-src/css/**/*.css'
    ])
        .pipe(less({compress: true}))
        .pipe(gulp.dest('web/css/'));
});

gulp.task('fonts', function () {
    return gulp.src([
        'bower_components/bootstrap/fonts/*',
        'bower_components/font-awesome/fonts/*',
        'web-src/fonts/*'
    ])
        .pipe(gulp.dest('web/fonts/'))
});

gulp.task('images', function () {
    return gulp.src([
        'web-src/images/**/*'
    ])
        .pipe(gulp.dest('web/images/'))
});

gulp.task('pages-js', function() {
    return gulp.src([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js',
        'web-src/js/**/*.js'
    ])
        .pipe(minifyJs())
        .pipe(gulp.dest('web/js/'));
});

gulp.task('clean', function () {
    return gulp.src(['web/css/*', 'web/js/*', 'web/fonts/*', 'font-awesome', 'web/images/*'])
        .pipe(clean());
});

gulp.task('default', ['clean'], function () {
    var tasks = ['less', 'css', 'fonts', 'pages-js', 'images'];
    tasks.forEach(function (val) {
        gulp.start(val);
    });
});

gulp.task('watch', function () {
    var less = gulp.watch('web-src/less/*.less', ['less']),
        js = gulp.watch('web-src/js/*.js', ['pages-js']);
});