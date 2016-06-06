var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/:id', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	request(config.server + "?service=Post.GetPostBase&post_id=" + req.param('id'),
		function(err, response, body) {
			if (!err && response.statusCode == 200) {
				var result = JSON.parse(body);
				console.log(result);
				if (result.ret == 200 && result.msg == "") {
					var page = agent.Mobile ? 'topicMobile' : 'topic';
					res.render(page, {
						'path':'../',
						result: result.data,
						'title':result.data.title
					});
				} else {
					console.error('topic failed:', err);
					next();
				}
			}
		});
});

module.exports = router;