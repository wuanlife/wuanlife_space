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
                console.error('CreatePlant failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('CreatePlant catch exception:', err.toString());
            }

        }

    });
});



module.exports = router;