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
            'user': req.session.user
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
            'user': req.session.user
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
            'user': req.session.user
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
            'user': req.session.user
        });
    } catch(e){
        next(e);
    }
});


router.post('/', function(req, res, next) {
    request.post({
        url: config.server + 'user/show_message',
        formData: {
            user_id: req.body.user_id,
            m_type: 4,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage catch exception:', err.toString());
            }

        }

    });
});
router.post('/post', function(req, res, next) {
    request.post({
        url: config.server + 'user/show_message',
        formData: {
            user_id: 1,
            m_type: 1,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage-post failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage-post catch exception:', err.toString());
            }

        }

    });
});

//获取星球通知相关
router.post('/planet', function(req, res, next) {
    request.post({
        url: config.server + 'user/show_message',
        formData: {
            user_id: 1,
            m_type: 2,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage-planet failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage-planet catch exception:', err.toString());
            }

        }

    });
});

//获取星球通知相关
router.post('/apply', function(req, res, next) {
    request.post({
        url: config.server + 'user/show_message',
        formData: {
            user_id: 1,
            m_type: 3,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage-apply failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage-apply catch exception:', err.toString());
            }

        }

    });
});


module.exports = router;