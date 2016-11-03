var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

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

router.get('/r', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var text = xss(req.query.text);
    var encodeText = encodeURIComponent(text);
    var page = agent.Mobile ? 'searchResultMobile' : 'searchResult';
    request(config.server + "?service=Group.Search&text=" + encodeText + "&gnum=3&gn=1&pnum=20&pn=1", function(error, response, body) {
        try {
            if (!error && response.statusCode == 200) {
                var result = JSON.parse(body);
                console.log(result);
                if (result.ret == 200) {
                    res.render(page, {
                        "searchText": text,
                        "title": "搜索结果",
                        "result": result.data,
                        "user": req.session.user,
                        "unsearchbtn":true
                    });
                } else {
                    res.render('error', {
                        'message': result.msg,
                        'error': {
                            'status': result.ret,
                            'stack': ''
                        }
                    });
                }
            } else {
                console.error('search failed:', error);
                next(error);
            }
        } catch (e) {
            console.error('search failed:', e);
            next(e);
        }
    });
});

router.get('/r/g', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var text = xss(req.query.text);
    var encodeText = encodeURIComponent(text);
    if (agent.Mobile) {
        request(config.server + "?service=Group.Search&text=" + encodeText + "&gnum=20&gn=1", function(error, response, body) {
            try {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    console.log(result);
                    if (result.ret == 200) {
                        res.render('srGroupMoblie', {
                            "searchText": text,
                            "title": "搜索结果",
                            "result": result.data,
                            "user": req.session.user,
                            "unsearchbtn": true
                        });
                    } else {
                        res.render('error', {
                            'message': result.msg,
                            'error': {
                                'status': result.ret,
                                'stack': ''
                            }
                        });
                    }
                } else {
                    console.error('search failed:', error);
                    next(error);
                }
            } catch (e) {
                console.error('search failed:', e);
                next(e);
            }
        });
    } else{
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