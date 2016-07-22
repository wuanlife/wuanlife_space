var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

/* GET users listing. */
router.get('/:groupid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	request(config.server + "?service=Post.GetGroupPost&group_id=" + req.params.groupid,
		function(error, response, body) {
			if (!error && response.statusCode == 200) {
				var result = JSON.parse(body);
				console.log(result);
				if (result.ret == 200 && result.msg == "") {
					var page = agent.Mobile ? 'groupMobile' : 'group';
					res.render(page, {
						'path': '../',
						result: result.data,
						'title': result.data.groupName,
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
				console.error('group failed:', error);
				next(error);
			}
		})
});

router.get('/:groupid/post', function(req, res, next) {
	if (req.session.user) {
		var agent = ua(req.headers['user-agent']);
		var userid = (req.session.user) ? req.session.user.userID : null;
		var page = agent.Mobile ? 'postMobile' : 'post';
		request(config.server + "?service=Group.GStatus&group_base_id=" + req.params.groupid + "&user_id=" + userid,
			function(error, response, body) {
				if (!error && response.statusCode == 200) {
					var result = JSON.parse(body);
					console.log(result);
					if (result.ret == 200 && result.msg == "" && result.data.code ==1) {
						res.render(page, {
							'path': '../../',
							'groupID': req.params.groupid,
							'title': '发表帖子',
							'user': req.session.user
						});
					} else {
						res.render('error', {
							'message': result.msg,
							error: {
								'status': 404,
								'stack': ''
							}
						});
					}
				} else {
					console.error('group failed:', error);
					next(error);
				}
			});
	} else {
		res.redirect('/login');
	}
});

router.post('/:groupid/post', function(req, res, next) {
	var userid = (req.session.user) ? req.session.user.userID : null;
	var html = xss(req.body.text, {
		onTag: function(tag, html, options) {
			if (tag == "strike") {
				return html;
			}
		}
	});
	request.post({
		url: config.server + '?service=Group.Posts',
		formData: {
			user_id: userid,
			group_base_id: req.params.groupid,
			title: xss(req.body.title),
			text: html
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (!err && httpResponse.statusCode == 200) {
			var result = JSON.parse(body);
			console.log(result);
			if (result.ret == 200 && result.msg == "" && result.data.code == 1) {
				res.redirect('/topic/' + result.data.info.post_base_id);
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
			next(err);
		}
	});

});

module.exports = router;