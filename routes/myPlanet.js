var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'myplanetMobile' : 'myplanet';
        res.render(page, {
            'result': null,
            'user': null
        });
    } catch(e){
        next(e);
    }
    
});

module.exports = router;