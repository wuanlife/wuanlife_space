var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var Promise = require('bluebird');
Promise.promisifyAll(request);

/* GET users listing. */
router.post('/:postid/reply', function(req, res, next) {
    if(!req.session.user) {
        res.send({
            ret: 403,
            msg: '未登录'
        });
        return;
    }
    request(`${config.server}post/get_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&pn=${req.body.replypn}`,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('get_index_post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('get_index_post error!  Server responded with:', body);
                try {
                    console.error('get_index_post failed:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(error) {
                    console.error('catch get_index_post exception:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        }
    )
});

router.get('/:postid', function(req, res, next) {
	/*
    if(!req.session.user) {
        res.send({
            ret: 403,
            msg: '未登录'
        });
        return;
    }
    console.log(`${config.server}post/get_post_base?user_id=${req.session.user.user_id}`);
    request(`${config.server}post/get_post_base?user_id=${req.session.user.user_id}&post_id=${req.body.postid}`,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('get_index_post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('get_index_post error!  Server responded with:', body);
                try {
                    console.error('get_index_post failed:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(error) {
                    console.error('catch get_index_post exception:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        }
    )

	*/
	console.log("userid:" + req.param("postid"));
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


module.exports = router;