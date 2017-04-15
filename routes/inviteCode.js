var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


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
	},function(err,httpResponse,body){
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');

		if(!err && httpResponse.statusCode === 200){
			var data = JSON.parse(body).data;
			console.log('data',data);
			console.log('body',body);
			if(data.code === 1){

			}else{

			}

			res.send(JSON.parse(body));
		}else{
			err && console.log('Get inviteCode Failed:',err.toString());
			res.send({
				ret: 500,
				msg: '服务器异常'
			});
		}
	});
});
module.exports = router;