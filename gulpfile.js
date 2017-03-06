var gulp        = require('gulp'),
    browserSync = require('browser-sync').create(),
    path = require('path'),
    reload = browserSync.reload,
    less  = require('gulp-less'),
    notify = require('gulp-notify'),
    plumber = require('gulp-plumber'),
    minifyCss = require("gulp-minify-css"),
    concat = require("gulp-concat"),
    watch = require('gulp-watch')

//path 定义
var basedir = './';
var publicdir = './public/';
var filepath = {
    'css' : path.join(publicdir,'stylesheets/dist/*.css'),
    'js' : path.join(publicdir,'javascripts/**/*.js'),
    'ejs': path.join(basedir,'views/**/*.ejs'),
    'less': path.join(publicdir,'stylesheets/src/less/!(global*).less'),
    'gLess': path.join(publicdir,'stylesheets/src/less/global*.less'),
    'pcCss': path.join(publicdir,'stylesheets/src/css/*PC.css'),
    'mCss' : path.join(publicdir,'stylesheets/src/css/*Mobile.css'),
    'srcStyle': path.join(publicdir,'stylesheets/src/css'),
    'style': path.join(publicdir,'stylesheets/dist')
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
        .pipe(watch(filepath.less))
        .pipe(plumber({errorHandler: notify.onError('Error: <%= error.message %>')}))
        .pipe(less())
        .pipe(gulp.dest(filepath.srcStyle))
 });

//编译globalLess
gulp.task('gLess',function(){
    gulp.src(filepath.gLess)
        .pipe(watch(filepath.gLess))
        .pipe(plumber({errorHandler: notify.onError('Error: <%= error.message %>')}))
        .pipe(less())
        .pipe(gulp.dest(filepath.style));

});

//合并PCcss文件
gulp.task('concatPCCss',function(){
    gulp.src(filepath.pcCss)
        .pipe(concat('style.css'))
        .pipe(gulp.dest(filepath.style));
});

//合并Mobilecss文件
gulp.task('concatMCss',function(){
    gulp.src(filepath.mCss)
        .pipe(concat('styleMobile.css'))
        .pipe(gulp.dest(filepath.style));
});

//监听less变化并合并css
gulp.task('watchLess',function(){
    gulp.watch(filepath.pcCss,['concatPCCss']);
    gulp.watch(filepath.mCss,['concatMCss']);
});

//默认任务
gulp.task('default',['watch','compileLess','gLess','watchLess']);