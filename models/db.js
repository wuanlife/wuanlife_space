    var config = require('../config/config'),
        Db = require('mongodb').Db,
        Connection = require('mongodb').Connection,
        Server = require('mongodb').Server;
    module.exports = new Db(config.db.db, new Server(config.db.host, config.db.port),
 {safe: true});