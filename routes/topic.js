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
				//console.log(result.data.posts[0]);
				if (result.ret == 200 && result.msg == "") {
					res.render('topic', {
						'path':'../',
						result: result.data,
						ag: agent,
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