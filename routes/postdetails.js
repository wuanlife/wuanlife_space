var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var Promise = require('bluebird');
Promise.promisifyAll(request);

/* GET users listing. */
router.post('/:postid/reply', function(req, res, next) {
    request(req.session.user ? 
        `${config.server}post/get_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&pn=${req.body.replypn}` :
        `${config.server}post/get_post_reply?post_id=${req.params.postid}&pn=${req.body.replypn}`,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('get_index_post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('get_index_post error!  Server responded with:', body);
                res.send({
                    ret: httpResponse.statusCode,
                    msg: JSON.parse(body).msg
                });

            }
        }
    )
});
router.post('/:postid', function(req, res, next) {
    request(req.session.user ? 
        `${config.server}post/get_post_base?user_id=${req.session.user.user_id}&post_id=${req.params.postid}` :
        `${config.server}post/get_post_base?post_id=${req.params.postid}`,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('get_index_post success!');
                return res.send(JSON.parse(body));
            } else {
                res.send(httpResponse.statusCode, JSON.parse(body));
            }
        }
    )
});
router.get('/:postid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'postMobile' : 'post';
		res.render(page, {
			'result': null,
			'user': req.session.user,
			'postid': req.param("postid"),
		});
	} catch(e){
		next(e);
	}
});
router.get('/:postid/edit', function(req, res, next) {

    var agent = ua(req.headers['user-agent']);
    try{
        var page = 'postEdit';
        res.render(page, {
            'result': null,
            'user': req.session.user,
            'postid': req.param("postid"),
        });
    } catch(e){
        next(e);
    }
});

router.post('/:postid/replysend', function(req, res, next) {
    if(!req.session.user) {
        return res.send({
            ret: 403,
            msg:'未登录'
        });    
    }
    request.post({
        url: config.server + 'post/post_reply',
        formData: {
            post_id: req.params.postid,
            user_id: req.session.user.user_id,
            p_text: req.body.p_text,
        }
    }, function optionalCallback(err, httpResponse, body) {
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

module.exports = router;