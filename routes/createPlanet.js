var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	res.render('createPlanet', {"ag":agent,'path':''});
});
router.post('/', function(req, res, next) {
	request.post({
		url: config.server + '/demo/?service=Group.Create',
		formData: {
			name: req.param('name'),
			user_id: req.param('user_id')
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