var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'mymessageMobile' : 'mymessage';
        res.render(page, {
            'result': null,
            'user': null
        });
    } catch(e){
        next(e);
    }
    
});
router.get('/post', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'mymessageMobile-post' : 'mymessage-post';
        res.render(page, {
            'result': null,
            'user': null
        });
    } catch(e){
        next(e);
    }
});
router.get('/planet', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'mymessageMobile-planet' : 'mymessage-planet';
        res.render(page, {
            'result': null,
            'user': null
        });
    } catch(e){
        next(e);
    }
});
router.get('/verify', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'mymessageMobile-verify' : 'mymessage-verify';
        res.render(page, {
            'result': null,
            'user': null
        });
    } catch(e){
        next(e);
    }
});



module.exports = router;