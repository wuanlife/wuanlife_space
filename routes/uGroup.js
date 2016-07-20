var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */

router.get('/:userid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	request(config.server + '?service=Post.GetMyGroupPost&id=' + req.param("userid"),
		function(error, response, body) {
			if (!error && response.statusCode == 200) {
				console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
				var data = JSON.parse(body);
				if (data.ret == 200 && data.msg == '') {
					var page = agent.Mobile ? 'uGroupMobile' : 'uGroup';
					res.render(page, {
						'path': '../',
						'result': data.data,
						'title': '我的星球',
						'userid': req.param("userid"),
						'user': req.session.user
					});
				} else {
					res.render('error', {
						'message': data.msg,
						error: {
							'status': data.ret,
							'stack': ''
						}
					});
				}
			} else {
				next(error);
			}
		})
});

router.get('/:userid/moreGroups', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    request(config.server + "?service=Group.GetCreate&user_id=" + req.param("userid"),
        function(error, response, body) {
            if (!error && response.statusCode == 200) {
                var result = JSON.parse(body);
                if (result.ret == 200 && result.msg == "") {
                    var page = agent.Mobile ? 'moreGroupsMobile' : 'moreGroups';
                    res.render(page, {
                        'path':'../../',
                        result: result.data,
                        'title':"我加入的星球",
                        'userid':req.param("userid"),
                        'user': req.session.user
                    });
                } else {
					res.render('error', {
						'message': result.msg,
						error: {
							'status': result.ret,
							'stack': ''
						}
					});
				}
            } else {
                console.error('CreateGroups failed:', error);
                next(error);
            }
        })
});
module.exports = router;