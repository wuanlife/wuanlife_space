var gulp        = require('gulp');
var browserSync = require('browser-sync').create();
var path = require('path');
var reload = browserSync.reload;
var less  = require('gulp-less');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');

//path 定义
var basedir = './';
var publicdir = './public/';
var filepath = {
    'css' : path.join(publicdir,'stylesheets/*.css'),
    'js' : path.join(publicdir,'javascripts/**/*.js'),
    'ejs': path.join(basedir,'views/**/*.ejs'),
    'less': path.join(publicdir,'stylesheets/*.less'),
    'style': path.join(publicdir,'stylesheets')
}
//监听任务
gulp.task('watch',function(){
    //设置代理模式并指定5000端口
    //监听files变化并自动刷新浏览器
    browserSync.init({
        proxy: 'http://localhost:3000',
        port: 5000,
        files: [filepath.css,filepath.js,filepath.ejs]
    });

    //自动刷新,可以通过自定义任务，在任务完成后再调用reload
    // gulp.watch('./public/**').on('change', reload);
});

//编译less
 gulp.task('compileLess',function(){
    gulp.src(filepath.less)
        .pipe(plumber({errorHandler: notify.onError('Error: <%= error.message %>')}))
        .pipe(less())
        .pipe(gulp.dest(filepath.style));
 });

//监听less变化
gulp.task('watchLess',function(){
    gulp.watch(filepath.less,['compileLess']);
});

//默认任务
gulp.task('default',['watch','watchLess']);