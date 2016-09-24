var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

/* GET users listing. */
router.get('/:groupid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']),
		pn = req.query.page || 1;
	var userid = (req.session.user) ? req.session.user.userID : null;
	request(config.server + "?service=Post.GetGroupPost&group_id=" + req.params.groupid + "&user_id=" + userid + "&pn=" + pn,
		function(error, response, body) {
			if (!error && response.statusCode == 200) {
				var result = JSON.parse(body);
				var creator = (userid == result.data.creatorID) ? 1:0;
				console.log(result);
				if (result.ret == 200 && result.msg == "") {
					var page = agent.Mobile ? 'groupMobile' : 'group';
					res.render(page, {
						result: result.data,
						'title': result.data.groupName,
						'creator':creator,
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
//星球发帖
router.get('/:groupid/post', function(req, res, next) {
	if (req.session.user) {
		var agent = ua(req.headers['user-agent']);
		var userid = req.session.user.userID;
		var page = agent.Mobile ? 'postMobile' : 'post';
		var tip = agent.Mobile ? 'tipMobile' : 'tip'
		request(config.server + "?service=Group.GStatus&group_base_id=" + req.params.groupid + "&user_id=" + userid,
			function(error, response, body) {
				if (!error && response.statusCode == 200) {
					var result = JSON.parse(body);
					//console.log(result);
					if (result.ret == 200 && result.msg == "") {
						if (result.data.code == 1) {
							res.render(page, {
								'groupID': req.params.groupid,
								'title': '发表帖子',
								'user': req.session.user
							});
						} else {
							res.render(tip, {
								'tip': '您没有权限操作',
								'title': '发表帖子',
								'user': req.session.user
							});
						}
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
		},
		onTagAttr:function(tag,name,value,isWhiteAttr){
			if (name == 'style') {
				return name + '="' + value + '"';
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
			//console.log(result);
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

//设置星球
router.get('/:groupid/set', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var userid = (req.session.user) ? req.session.user.userID : null;
	var page = agent.Mobile ? 'setGroupMobile' : 'setGroup';
	var tip = agent.Mobile ? 'tipMobile' : 'tip'
	request(config.server + "?service=Group.GetGroupInfo&group_id=" + req.params.groupid + "&user_id=" + userid,
		function(error, response, body) {
			if (!error && response.statusCode == 200) {
				var result = JSON.parse(body);
				//console.log(result);
				if (result.ret == 200) {
					if (result.data.creator == 1) {
						res.render(page, {
							'result': result.data,
							'title': '星球设置',
							'user': req.session.user
						});
					} else {
						res.render(tip, {
							'tip': '您没有权限操作',
							'title': '星球设置',
							'user': req.session.user
						});
					}
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
		});
});

router.post('/:groupid/set', function(req, res, next) {
	var userid = (req.session.user) ? req.session.user.userID : null;
	request.post({
		url: config.server + '?service=Group.alterGroupInfo',
		formData: {
			user_id: userid,
			group_id: req.params.groupid,
			g_introduction: xss(req.body.g_introduction),
			g_image: xss(req.body.g_image)
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (!err && httpResponse.statusCode == 200) {
			var result = JSON.parse(body);
			//console.log(result);
			if (result.ret == 200  && result.data.data == 1) {
				res.redirect('/group/' + req.params.groupid);
			} else {
				res.render('error', {
					'message': result.msg,
					error: {
						'status': result.ret,
						'stack': result.data.msg
					}
				});
			}
		} else {
			next(err);
		}
	});

});


module.exports = router;