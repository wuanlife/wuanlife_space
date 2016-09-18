var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');
// var formidable = require("formidable");

/* GET users listing. */
router.get('/', function(req, res, next) {
	if (req.session.user) {
		var agent = ua(req.headers['user-agent']);
		var page = agent.Mobile ? 'createGroupMobile' : 'createGroup';
		res.render(page, {
			'path': '',
			'title': '创建星球',
			'user':req.session.user
		});
	} else{
		res.redirect('/login');
	}
});
router.post('/', function(req, res, next) {
	request.post({
		url: config.server + '?service=Group.Create',
		formData: {
			name: req.param('name'),
			user_id: req.session.user.userID,
			g_image: xss(req.param('g_image')),
			g_introduction:xss(req.param('g_introduction')),
			private:req.body.private || 0
		}
	}, function optionalCallback(err, httpResponse, body) {
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		if (!err && httpResponse.statusCode == 200) {
			//console.log('CreatePlanet successful!  Server responded with:', body);
			 return res.send(JSON.parse(body));
		}
		console.error('CreatePlant failed:', err.toString());
		res.send({
			ret: 500,
			msg:'服务器异常'
		});
	});
})
module.exports = router;