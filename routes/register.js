var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */

router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var page = agent.Mobile ? 'registerMobile' : 'register';
	res.render(page, {
		'path':'',
		'title':'注册',
		'user':req.session.user
	});
});
router.post('/', function(req, res, next) {
	request.post({
		url: config.server + '?service=User.Reg',
		formData: {
			Email: req.body.email,
			password: req.body.password,
			nickname: req.body.nickname
		}
	}, function optionalCallback(err, httpResponse, body) {
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		if (err) {
			console.error('register failed:', err);
			 return res.send({
				ret: 500,
				msg:'服务器异常'
			});
		}
		req.session.regenerate(function() {
			req.session.user = JSON.parse(body).data.info;
			req.session.save(); //保存一下修改后的Session
			//console.log('Register successful!  Server responded with:', body);
			res.send(JSON.parse(body));
		});	
	});

});

module.exports = router;