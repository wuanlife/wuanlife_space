var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'addPlanetMobile' : 'addPlanet';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	

});



router.post('/',function(req,res,next){
	request(config.server + 'group/join?user_id=' + req.body.userId + '&group_id=' + req.body.groupId,
	function optionalCallback(err, httpResponse, body) {
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		console.log('err',err);
		console.log('body',body);
		if (!err && httpResponse.statusCode == 200) {
			var data = JSON.parse(body).data;
			console.log('mdzz',data);
			if(data.code == 1) {
				console.log('Add group successful!  Server responded with:', body);
			} else {
				console.error('Add failed:', body);
			}
			console.log('body',JSON.parse(body));
			res.send(JSON.parse(body));
		} else{
			err && console.error('Login failed:', err.toString());
			res.send({
				ret:500,
				msg:'服务器异常'
			});
		}
	});	
});
module.exports = router;