var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	console.log('start bug');
	var agent = ua(req.headers['user-agent']),
        email = req.flash('email').toString(),
        error = req.flash('error').toString(),
        verify = req.flash('verify').toString(),
        page;

	if(verify){

		console.log('bug get1');
		page = agent.Mobile ? 'modifyPwdMobile' :'modifyPwd';
	}else{
		console.log('bug get2');
		page = agent.Mobile ? 'retrievePwdMobile' :'retrievePwd';
	}
	try{
		res.render(page, {
			'title':'重置密码',
			'user' : req.session.user,
			'email': email,
			'error': error
		});
		console.log('bug get3');
	} catch(e){
		next(e);
	}
});

//发送邮箱验证，用于更改密码
router.post('/',function(req,res,next){

	var email = req.body.email;
	console.log('email',email);
	request.get({
		url:config.server + 'user/sendmail',
		formData:{
			user_email: req.body.email,
		}
	},function(err,httpResponse,body){
		//console.log('responded',httpResponse);
		console.log('err',err);
		console.log('body',body);
		console.log('httpResponse.status',httpResponse.status);
		if(!err && httpResponse.status === 200){
			var result = JSON.parse(body);
			console.log('bug1');
			if(result.ret == 200){
				if(result.data.code == 1){
					//flash刷新页面
					req.flash('change_pwd',true);
					//req.session.verify = true;
					console.log('bug2');
				}else{
					req.flash('error',req.data.msg);
					//req.session.error = req.data.msg;
					console.log('bug3');
				}

				req.flash('email',email);
				//req.session.email = email;
				res.redirect('back');
				console.log('bug4');
			}else{
				res.render('error',{
					'massage':result.msg,
					error:{
						'status':result.ret,
						'stack':''
					}
				});
				console.log('bug5');
			}
		}else{
		    console.log('错误的跳转',err);
			next(err);
		}
	});
});


//更改密码
router.post('/set', function(req, res, next) {
    request.post({
        url: config.server + 'user/repsw',
        formData: {
            user_email: req.body.email,
            i_code:req.body.inviteCode,
            password:req.body.password,
            psw:req.body.psw
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (err) {
            console.error('email send failed:', err);
            return res.send({
                ret: 500,
                msg:'服务器异常'
            });
        }
        console.log('Reset Password successful!  Server responded with:', body);
        res.send(JSON.parse(body));
    });
});

module.exports = router;