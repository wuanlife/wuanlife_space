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
		res.render(page, {
			'title': '验证邮箱',
			'user':req.session.user
		});
	} else{
		res.redirect('/login');
	}
});

router.post('/', function(req, res, next) {
	request.post({
		url: config.server + '?service=User.CheckMail',
		formData: {
			Email: req.param('Email'),
			code: req.param('code')
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (err) {
			console.error('CreatePlant failed:', err);
			//			res.header('Content-type', 'application/json');
			//			res.header('Charset', 'utf8');
			//			res.send({
			//				err: err
			//			});
			next(err);
		}
		console.log('CreatePlanet successful!  Server responded with:', body);
		
		res.header('Content-type', 'application/json');
	    res.header('Charset', 'utf8');
	    res.send(JSON.parse(body));

	});
});
module.exports = router;