var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */

router.get('/', function(req, res, next) {
	if (req.session.user) {
		//请求当前用户信息
		request(config.server + '?service=User.getUserInfo&user_id=' + req.session.user['userID'],
			function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					var data = JSON.parse(body);
					if (data.ret == 200 && data.msg == '') {
						var agent = ua(req.headers['user-agent']);
	    				var page = agent.Mobile ? 'changeProfileMobile' : 'changeProfile';

						res.render(page, {
							'result': data.data,
							'title': data.data.user_name,
							'userID': data.data.userID,
							'Email': data.data.Email,
							'nickname': data.data.nickname,
	                        'sex': data.data.sex,
	                        'year': data.data.year,
	                        'month': data.data.month,
	                        'day': data.data.day,
							'user': req.session.user,
							'mailChecked': data.data.mailChecked
						});
					} else {
						res.render('error', {
							'message': data.msg,
							error: {
								'status': data.ret,
								'stack': ''
							}
						});
					}
				} else {
					next(error);
				}
			}
		)		
		/*
		var agent = ua(req.headers['user-agent']);
	    var page = agent.Mobile ? 'changeProfileMobile' : 'changeProfile';
	    res.render(page, {'title':'修改个人信息','user':req.session.user});
	    */
	} else{
		res.redirect('/login');
	}
});

router.post('/', function(req, res, next) {
	request(config.server + "?service=User.alterUserInfo&" +
				"user_id=" + req.body.userID +
				"&sex=" + req.body.sex + 
				"&year=" + req.body.year + 
				"&month=" + req.body.month + 
				"&day=" + req.body.day,
		function(error, response, body) {
			if (error) {
				console.error('changeProfile failed:', err);
				//			res.header('Content-type', 'application/json');
				//			res.header('Charset', 'utf8');
				//			res.send({
				//				err: err
				//			});
				next(err);
			}
			else {
				console.log('changeProfile successful!  Server responded with:', body);
				res.header('Content-type', 'application/json');
		        res.header('Charset', 'utf8');
		        res.send(JSON.parse(body));
	    	}
		})
});


module.exports = router;