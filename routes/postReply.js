var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');

router.post('/', function(req, res, next) {
    request.post({
        url: config.server + '?service=post.Postreply',
        formData: {
            user_id: req.param('user_id'),
            post_id: req.param('post_id'),
            text: req.param('text')
        }    
    }, function optionalCallback(err, httpResponse, body) {
        if (err) {
            console.error('Reply failed:', err);
            res.header('Content-type', 'application/json');
            res.header('Charset', 'utf8');
            res.send({
                err: err
            });
        }
        console.log('Reply success:', JSON.parse(body));
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        res.send(JSON.parse(body));
    });

});

module.exports = router;