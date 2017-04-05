var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'loginMobile' : 'login';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	
});

router.post('/', function(req, res, next) {

	//如果用户已经用此账号登陆
	if(req.session.user ? req.body.email == req.session.user.user_email:false){
		//重定向到index.
	}else{
		request.post({
			url: config.server + 'user/login',
			formData: {
				user_email: req.body.email,
				password: req.body.password
			}
		}, function optionalCallback(err, httpResponse, body) {
			res.header('Content-type', 'application/json');
			res.header('Charset', 'utf8');
			console.log('err',err);
			console.log('status',httpResponse.statusCode);
			if (!err && httpResponse.statusCode == 200) {
				var data = JSON.parse(body).data;
				console.log('mdzz',data);
				if(data.code == 1) {
					req.session.user = data.info;
					req.session.save(); //保存一下修改后的Session
					console.log('Login successful!  Server responded with:', body);
				} else {
					console.error('Login failed:', body);
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

	}

	

});
module.exports = router