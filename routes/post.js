var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

/* GET users listing. */
router.get('/:groupid', function(req, res, next) {
	if (req.session.user) {
		var agent = ua(req.headers['user-agent']);
		var page = agent.Mobile ? 'postMobile' : 'post';
		res.render(page, {
			'path': '../',
			'groupID': req.params.groupid,
			'title': '发表帖子',
			'user': req.session.user
		});
	} else{
		res.redirect('/login');
	}
});

router.post('/:groupid', function(req, res, next) {
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
			if (result.ret == 200 && result.msg == "") {
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