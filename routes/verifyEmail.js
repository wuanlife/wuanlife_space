var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
// var formidable = require("formidable");

/* GET users listing. */
router.get('/', function(req, res, next) {
	if (req.session.user) {
		var agent = ua(req.headers['user-agent']);
		var page = agent.Mobile ? 'verifyEmailMobile' : 'verifyEmail';
		var tip = agent.Mobile ? 'tipMobile' : 'tip';
		request(config.server + "?service=User.GetMailChecked&user_id=" + req.session.user.userID,
			function(error, response, body) {
				if (!error && response.statusCode == 200) {
					var result = JSON.parse(body);
					//console.log(result);
					if (result.ret == 200 && result.data) {
						if (result.data.mailChecked == 1) {
							res.render(tip, {
								'tip': '您的邮箱已通过验证，无需再次验证',
								'title': '验证邮箱',
								'user': req.session.user
							});
						} else {
							res.render(page, {
								'title': '验证邮箱',
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
					console.error('verify failed:', error);
					next(error);
				}
			});
	} else{
		res.redirect('/login');
	}
});

router.post('/', function(req, res, next) {
	request.post({
		url: config.server + '?service=User.MailChecked',
		formData: {
			Email: req.session.user.Email,
			code: req.body.code
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (err) {
			console.error('Check email failed:', err);
			next(err);
		}
		//console.log('Check Email successful!  Server responded with:', body);
		res.header('Content-type', 'application/json');
	    res.header('Charset', 'utf8');
	    res.send(JSON.parse(body));

	});
});
module.exports = router;