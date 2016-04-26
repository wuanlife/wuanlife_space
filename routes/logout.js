var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');

router.get('/', function(req, res, next) {
    request(config.server + 'demo/?service=User.Logout',
        function(error, response, body) {
            if (!error && response.statusCode == 200) {
                    console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                    res.header('Content-type', 'application/json');
                    res.header('Charset', 'utf8');
                    res.send(body);
                }
        })
});

module.exports = router;