var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
//var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'inviteCodeMobile' : 'inviteCode';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	

});



router.post('/',function(req,res,next){
	
	request.post({
		url:config.server + 'user/show_code',
		formData:{
			user_id: req.body.userId
		}
	},function(){
		
	});
});
module.exports = router;