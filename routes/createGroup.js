var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');
// var formidable = require("formidable");

/* GET users listing. */
router.get('/', function(req, res, next) {
	//
	if (req.session.user) {
		var agent = ua(req.headers['user-agent']);
		var page = agent.Mobile ? 'creatGroupM' : 'createGroup';
		res.render(page, {
			'path': '',
			'title': '创建星球',
			'user':req.session.user
		});
	} else{
		res.redirect('/login');
	}
});
//创建星球提交的数据
router.get('/sub',function(req,res,next){
	if(req.session.user){
		var userid=req.session.user.user_id,
		    g_name=encodeURIComponent(req.query.g_name),
		    g_image=req.query.g_image,
		    g_introduction=encodeURIComponent(req.query.g_introduction),
		    private=req.query.private;
		request(config.server+'group/create?user_id='+userid+'&g_name='+g_name+'&g_image='+g_image+'&g_introduction='+g_introduction+'&private='+private,
			function(error, response, body){
				if (!error && response.statusCode == 200) {
					var result = JSON.parse(body);
					if (result.ret == 200) {
						res.send(result);
					} else {
						res.render('error', {
							'message': result.msg,
							error: {
								'status': result.ret,
								'stack': ''
							}
						});
					}
			} else {
				console.error('group failed:', error);
				next(error);
			}
			});
	}else{}
});
module.exports = router;