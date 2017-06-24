var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');

/* GET home page. */

router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var userID = (req.session.user) ? req.session.user.user_id : null;
    console.log(userID);
	try{
		var page = agent.Mobile ? 'indexMobile' : 'index';
		res.render(page, {
			'result': null,
			'user': req.session.user
		});
	} catch(e){
		next(e);
	}
	

});

router.post('/', function(req, res, next) {
    let latest = req.body.loadlatest || 1
    request({
            url: `${config.server}post/get_index_post?pn=${req.body.pn}&latest=${latest}`,
            headers:{'Access-Token':req.session.user ? req.session.user['Access-Token'] : ''},
        },
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('get_index_post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('get_index_post error!  Server responded with:', body);
                res.send(httpResponse.statusCode, {
                    ret: httpResponse.statusCode,
                    msg: JSON.parse(body)
                });
            }
        }
    )
});


router.post('/approve', function(req, res, next) {
    if(!req.session.user) {
        res.send({
            ret: 403,
            msg: '未登录'
        });
        return;
    }
    request(config.server + "Post/approve_post?post_id=" + req.body.postid + "&user_id=" + req.session.user.user_id + "&floor=1",
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('approve a post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('approve a post error!  Server responded with:', body);
                try {
                    console.error('approve a post failed:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(error) {
                    console.error('catch approve a post exception:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        }
    );
});
router.post('/collect', function(req, res, next) {
    if(!req.session.user) {
        res.send({
            ret: 403,
            msg: '未登录'
        });
        return;
    }
    request(config.server + "Post/collect_post?post_id=" + req.body.postid + "&user_id=" + req.session.user.user_id,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('collect a post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('collect a post error!  Server responded with:', body);
                try {
                    console.error('collect a post failed:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(error) {
                    console.error('catch collect a post exception:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        }
    )
});

module.exports = router;