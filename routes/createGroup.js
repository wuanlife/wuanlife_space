var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');
var formidable = require("formidable");

/* GET users listing. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var page = agent.Mobile ? 'createGroupMobile' : 'createGroup';
	res.render(page, {'path':'','title':'创建星球'});
});
router.post('/', function(req, res, next) {
	// var body = req.param('g_image'),
 //  		base64Data = body.replace(/^data:image\/png;base64,/,"") + ".png",
 //  		binaryData = new Buffer(base64Data, 'base64');
 //  		console.log(binaryData);
	request.post({
		url: config.server + '?service=Group.Create',
		formData: {
			name: req.param('name'),
			user_id: req.param('user_id'),
			g_image:req.param('g_image'),
			g_introduction:xss(req.param('g_introduction'))
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
		// res.render('/planetDetail/2' {});
	});
})
module.exports = router;