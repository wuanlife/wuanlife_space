var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var xss = require('xss');

/* GET users listing. */
router.get('/:method', function(req, res, next) {
	var data = {};
	var userID = (req.session.user) ? req.session.user.userID : null;
	switch(req.params.method) {
		case 'show_message-post':
			var pn = req.param('currentpage');
			request(config.server + '/user/show_message?user_id=82&pn=1&m_type=1', function(error, response, body) {
				res.header('Content-type', 'application/json');
				res.header('Charset', 'utf8');
				try {
					if(!error && response.statusCode == 200) {
						//console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
						res.send(JSON.parse(body));
					}
				} catch(e) {
					res.send({
						ret: 500,
						msg: '服务器异常'
					});
				}
			});
			break;
		default:
			res.send('respond with a resource');
			break;
	}
});
router.post('/:method', function(req, res, next) {
	var data = {};
	var userID = (req.session.user) ? req.session.user.userID : null;
	var email = (req.session.user) ? req.session.user.Email : null;
	switch(req.params.method) {
		/*
		//帖子置顶
		case 'stickyPost':
			request.post({
				url: config.server + '?service=Post.StickyPost',
				formData: {
					user_id: userID,
					post_id: req.body.post_id
				}
			}, function optionalCallback(err, httpResponse, body) {
				res.header('Content-type', 'application/json');
				res.header('Charset', 'utf8');
				try {
					if(!err && httpResponse.statusCode == 200) {
						return res.send(JSON.parse(body));
					}
					console.error('Sticky failed:', err);
					res.send({
						ret: 500,
						msg: '服务器异常'
					});
				} catch(e) {
					res.send({
						ret: 500,
						msg: e.message
					});
				}
			});
			break;
		*/
		default:
			res.send('respond with a resource');
			break;
	}

});
module.exports = router;