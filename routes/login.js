var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var page = agent.Mobile ? 'loginMobile' : 'login';
	res.render(page, {'path':'','title':'登录'});
});
router.post('/', function(req, res, next) {
	request.post({
		url: config.server + 'demo/?service=User.Login',
		formData: {
			Email: req.param('email'),
			password: req.param('password')
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (err) {
			console.error('Login failed:', err);
			res.header('Content-type', 'application/json');
			res.header('Charset', 'utf8');
			res.send({
				err: err
			});
		}

		// req.session.regenerate(function(){
  //         req.session.user = JSON.parse(body).data.info;;
  //         req.session.save();  //保存一下修改后的Session
  //     });
		console.log('Login successful!  Server responded with:', JSON.parse(body));
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		res.send(JSON.parse(body));
	});

});

module.exports = router;