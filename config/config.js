var path = require('path'),
    rootPath = path.normalize(__dirname + '/..'),
    env = process.env.NODE_ENV || 'development';

var config = {
    development: {
        root: rootPath,
        app: {
            name: 'wuan_yo'
        },
        port: process.env.PORT || 3000,
        //db: 'mongodb://localhost:27017/test-app',
        db: {
            cookieSecret: 'myapp',
            db: 'wuanDB',
            host: 'localhost',
            port: 27017
        },
        //server: 'http://www.wuanla.com:800/',
        server: 'http://dev.wuanlife.com:800/',
        'ACCESS_KEY': 'fOCmqJDZvBUZCL9lGSbN1sl1_cVNuV7f7ns0bcfs',
        'SECRET_KEY': 'GWhI_igD2NcqaomXi0fv8R_j8fnGVvH6tJPLwLFk',
        'Bucket_Name': 'wuanlife',
        'Uptoken_Url': 'uptoken',
        'Domain': 'http://qiniu-plupload.qiniudn.com/'
    },

    test: {
        root: rootPath,
        app: {
            name: 'wuan_yo'
        },
        port: process.env.PORT || 3000,
        db: 'mongodb://localhost:27017/test-app',
        server: 'http://dev.wuanlife.com:800/'
    },

    production: {
        root: rootPath,
        app: {
            name: 'wuan_yo'
        },
        port: process.env.PORT || 3000,
        db: 'mongodb://localhost/yoexpress-production',
        server: 'http://www.wuanla.com:800/',
        'ACCESS_KEY': 'fOCmqJDZvBUZCL9lGSbN1sl1_cVNuV7f7ns0bcfs',
        'SECRET_KEY': 'GWhI_igD2NcqaomXi0fv8R_j8fnGVvH6tJPLwLFk',
        'Bucket_Name': 'wuanlife',
        'Uptoken_Url': 'uptoken',
        'Domain': 'http://qiniu-plupload.qiniudn.com/'
    }
};

module.exports = config[env];