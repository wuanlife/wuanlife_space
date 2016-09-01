var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var Promise = require('bluebird');
Promise.promisifyAll(request);

/* GET users listing. */

router.get('/:userid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']),
        pn = req.query.page || 1;
	request(config.server + '?service=Post.GetMyGroupPost&id=' + req.param("userid") + '&pn=' + pn,
		function(error, response, body) {
			if (!error && response.statusCode == 200) {
				//console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
				var data = JSON.parse(body);
				if (data.ret == 200 && data.msg == '') {
					var page = agent.Mobile ? 'uGroupMobile' : 'uGroup';
					res.render(page, {
						'path': '../',
						'result': data.data,
						'title': data.data.user_name + '的星球',
						'userid': req.param("userid"),
                        'ownerName':data.data.user_name,
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
    var agent = ua(req.headers['user-agent']),
        pn = req.query.page || 1;
    Promise.all([request.getAsync(config.server + "?service=Group.GetCreate&user_id=" + req.param("userid") + "&page=" + pn),
        request.getAsync(config.server + "?service=Group.GetJoined&user_id=" + req.param("userid") + "&page=" + pn)]).spread(function(create,joined){
            var createResult = JSON.parse(create.body);
            var joinedResult = JSON.parse(joined.body);
            // console.log(createResult);
            // console.log(joinedResult);
            if (createResult.ret == 200 && joinedResult.ret == 200) {
                var page = agent.Mobile ? 'moreGroupsMobile' : 'moreGroups';
                return res.render(page, {
                    cResult: createResult.data,
                    jResult: joinedResult.data,
                    title: createResult.data.user_name + "加入的星球",
                    userid: req.param("userid"),
                    ownerName: createResult.data.user_name,
                    'user': req.session.user
                });
            } else{
                return res.render('error', {
                    message: createResult.msg || joinedResult.msg,
                    error: {
                        'status': 404,
                        'stack': ''
                    }
                });
            }
        }).catch(function(error){
            console.error('moreGroups failed:', error);
            next(error);
        });
});
module.exports = router;