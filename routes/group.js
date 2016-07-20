var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/:groupid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	request(config.server + "?service=Post.GetGroupPost&group_id=" + req.param("groupid"),
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

module.exports = router;