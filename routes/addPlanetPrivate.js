var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
//var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'addPlanetPrivateMobile' : 'addPlanetPrivate';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	

});
module.exports = router;