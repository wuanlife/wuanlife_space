var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/:groupid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	request(config.server + "demo/?service=Post.GetGroupPost&group_id=" + req.param("groupid"),
		function(error, response, body) {
			if (!error) {
				console.log('Planet Detail List Success:OK');
				var result = JSON.parse(body);
				console.log(result);
				var getText = function(post){
					post.text = post.text.replace(/<.+?>/g,"");
					return post;
				}
				result.data.posts.map(getText);
				console.log(result.data.posts[0]);
				if (result.ret == 200 && result.msg == "") {
					//res.render('planetDetail', result.data);
					res.render('group', {
						'path':'../',
						result: result.data,
						ag: agent,
						'title':result.data.groupName
					});
				}
			} else {
				console.error('group failed:', err);
				next(error);
			}
		})
});

module.exports = router;