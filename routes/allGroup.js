var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']),
		pn = req.query.page || 1;
	request(config.server + "?service=Group.Lists&page=" + pn, function(error, response, body) {
		if (!error) {
			var result = JSON.parse(body);
			//console.log(result);
			if (result.ret == 200 && result.msg == "") {
				var page = agent.Mobile ? 'allGroupMobile' : 'allGroup';
				res.render(page, {
					'path':'',
					result: result.data,
					'title':'全部星球',
					'user':req.session.user
				});
			} else{
				res.render('error', {
					'message': result.msg,
					error: {
						'status': result.ret,
						'stack': ''
					}
				});
			}
		} else {
			console.error('allGroup failed:', error);
			next(error);
		}
	})
});
module.exports = router;