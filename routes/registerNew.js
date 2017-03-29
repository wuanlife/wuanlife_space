var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'registerMobile' : 'register';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	

});



router.post('/', function(req, res, next) {

	request.post({
		url:config.server + 'user/reg',
		formData:{
			user_name:  req.body.nickname,
			user_email: req.body.email,
			password:   req.body.password,
			i_code:     req.body.inviteCode
		},
	},function(err,response,body){
		
		res.header('Content-type','application/json');
		res.header('Charset','utf8');
		if(!err && response.statusCode === 200){
			var data = JSON.parse(body).data;
			if(data.code === 1){
				req.session.regenerate(function(){
					req.session.user = data.info;
					req.session.save();
					console.log('register successful! responded ' ,body);
				});
			}else{
				console.error('register failed!',body);
			}

			res.send(JSON.parse(body));
		}else{
			console.error('Register failed!',err.toString());
			res.send({
				ret:500,
				msg:'服务器异常，注册一场',
			});
		}
	});

});
module.exports = router;