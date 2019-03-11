var gulp = require('gulp');

var concatCss = require('gulp-concat-css');
var minifyCss = require('gulp-minify-css');

var listCss = [
	'project/app/webroot/css/bootstrap.custom.css',
	'project/app/webroot/css/all.reset.css',
	'project/app/webroot/css/all.base.css',
	'project/app/webroot/css/all.form.css',
	'project/app/webroot/css/fileuploader.css',
	'project/app/webroot/css/fileuploader.webrsa.css',
	'project/app/webroot/css/permissions.css',
	'project/app/Plugin/Configuration/webroot/css/configuration_parser.css',
	'project/app/webroot/css/menu.css',
	'project/app/webroot/css/popup.css',
	'project/app/webroot/css/print.generic.css',
	'project/app/webroot/css/screen.generic.css',
	'project/app/webroot/css/screen.search.css'
];

var uglify = require('gulp-uglify');
var concatJs = require('gulp-concat');

var listJs = [
	'project/app/webroot/js/prototype.js',
	'project/app/webroot/js/webrsa.extended.prototype.js',
	'project/app/webroot/js/prototype.livepipe.js',
	'project/app/webroot/js/prototype.tabs.js',
	'project/app/webroot/js/tooltip.prototype.js',
	'project/app/webroot/js/webrsa.custom.prototype.js',
	'project/app/webroot/js/webrsa.common.prototype.js',
	'project/app/webroot/js/prototype.event.simulate.js',
	'project/app/webroot/js/dependantselect.js',
	'project/app/webroot/js/prototype.maskedinput.js',
	'project/app/webroot/js/webrsa.validaterules.js',
	'project/app/webroot/js/webrsa.validateforms.js',
	'project/app/webroot/js/webrsa.additional.js',
	'project/app/webroot/js/fileuploader.js',
	'project/app/webroot/js/fileuploader.webrsa.js',
	'project/app/webroot/js/cake.prototype.js',
	'project/app/webroot/js/webrsa.cake.tabbed.paginator.js',
	'project/app/webroot/js/prototype.fabtabulous.js',
	'project/app/webroot/js/prototype.tablekit.js',
	'project/app/Plugin/Configuration/webroot/js/prototype.configuration-parser.js'
];


gulp.task('css', function () {
  return gulp.src(listCss)
    .pipe(concatCss("webrsa.css"))
    .pipe(minifyCss())
    .pipe(gulp.dest('project/app/webroot/css/'));
});

gulp.task('watch-css', function () {
    var onChange = function (event) {
        console.log('File '+event.path+' has been '+event.type);
    };

    gulp.watch(listCss, gulp.parallel('css'))
        .on('change', onChange);
});

gulp.task('js', function () {
  return gulp.src(listJs)
    .pipe(concatJs('webrsa.js'))
    .pipe(gulp.dest('project/app/webroot/js/'));
});

gulp.task('watch-js', function () {
    var onChange = function (event) {
        console.log('File '+event.path+' has been '+event.type);
    };

    gulp.watch(listJs, gulp.parallel('js'))
        .on('change', onChange);
});

gulp.task('default', gulp.parallel('css', 'js', 'watch-css', 'watch-js'));
