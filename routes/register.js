var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	res.render('register', {
		'path':'',
		ag: agent,
		'title':'注册'
	});
});
router.post('/', function(req, res, next) {
	request.post({
		url: config.server + 'demo/?service=User.Reg',
		formData: {
			Email: req.param('email'),
			password: req.param('password'),
			nickname: req.param('nickname')
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
		console.log('Register successful!  Server responded with:', body);
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		res.send(JSON.parse(body));
	});

});

module.exports = router;