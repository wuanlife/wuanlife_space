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
        db: 'mongodb://localhost:27017/test-app',
        server: 'http://dev.wuanlife.com/'
    },

    test: {
        root: rootPath,
        app: {
            name: 'wuan_yo'
        },
        port: process.env.PORT || 3000,
        db: 'mongodb://localhost:27017/test-app',
        server: 'http://dev.wuanlife.com/'
    },

    production: {
        root: rootPath,
        app: {
            name: 'wuan_yo'
        },
        port: process.env.PORT || 3000,
        db: 'mongodb://localhost/yoexpress-production',
        server: 'http://dev.wuanlife.com/'
    }
};

module.exports = config[env];