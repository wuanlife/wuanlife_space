var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');

/* GET home page. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']),
		pn = req.query.page || 1;
	request(config.server + '?service=Post.GetIndexPost&pn=' + pn, function(error, response, body) {
		if (!error && response.statusCode == 200) {
			//console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
			var data = JSON.parse(body);
			if (data.ret == 200) {
				var page = agent.Mobile ? 'indexMobile' : 'index';
				res.render(page, {
					'path':'',
					'result': data.data,
					'title':'首页',
					'user':req.session.user
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
			//			res.status(err.status || 500);
			//			res.render('error', {
			//				message: response.statusCode + '/n服务器忙，请稍后再试！',
			//				error: {}
			//			});
			next(error);
		}
	})

});

module.exports = router;