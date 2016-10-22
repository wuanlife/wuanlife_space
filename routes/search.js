var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    if (agent.Mobile) {
        try {
            res.render('searchMobile', {
                'title': '搜索',
                'user': req.session.user,
                'unsearchbtn':true
            });
        } catch (e) {
            next(e);
        }
    } else {
        res.render('error', {
            'message': 'no page',
            'error': {
                'status': 404,
                'stack': ''
            }
        });
    }
});

module.exports = router;